<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OnlineRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LeaveRequestNotification;
use App\Notifications\PaymentRequestNotification;
use App\Notifications\OvertimeRequestNotification;

class OnlineController extends Controller
{
    /**
     * Show Online Work form
     */
    public function showForm()
    {
        return view('online.online')->with('title', 'Online Work');
    }

    /**
     * Submit Payment Request Form
     */
    public function submitForm(Request $request)
    {
        $request->validate([
            'department' => 'required',
            'date' => 'required|date',
            'contract_name' => 'required',
            'amount' => 'required|numeric',
            'completed_work' => 'required',
            'manager' => 'required',
        ]);

        // Check monthly budget (placeholder - would need CRM integration)
        $monthlyTotal = OnlineRequest::where('user_email', Auth::user()->email)
            ->where('type', 'payment_request')
            ->whereMonth('created_at', now()->month)
            ->get()
            ->sum(function($record) {
                $data = $record->form_data;
                return is_array($data) && isset($data['amount']) ? $data['amount'] : 0;
            });
            
        $monthlyBudget = 5000; // This should come from CRM

        if (($monthlyTotal + $request->amount) > $monthlyBudget) {
            return response()->json([
                'success' => false,
                'message' => 'Request exceeds monthly budget. Remaining budget: ' . ($monthlyBudget - $monthlyTotal)
            ]);
        }

        $onlineRequest = OnlineRequest::create([
            'type' => 'payment_request',
            'form_data' => json_encode($request->all()),
            'status' => 'pending',
            'user_email' => Auth::user()->email,
        ]);

        if($onlineRequest) {
            // Notify supervisors and HR
            $supervisors = User::where('role', 'supervisor')->get();
            $hr = User::where('role', 'hr')->get();
            
            Notification::send($supervisors, new PaymentRequestNotification($onlineRequest));
            Notification::send($hr, new PaymentRequestNotification($onlineRequest));

            return response()->json([
                'success' => true,
                'message' => 'Payment request submitted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to save request'
        ]);
    }

    /**
     * Submit Working Hours
     */
    public function submitWorkingHours(Request $request)
    {
        $request->validate([
            'work_date' => 'required|date',
            'team' => 'required',
        ]);

        // Calculate total hours based on completed tasks
        $totalHours = 0;
        if ($request->has('working_hours')) {
            foreach ($request->working_hours as $hour => $task) {
                if (!empty(trim($task))) {
                    $totalHours += 2; // Each time slot is 2 hours
                }
            }
        }

        // Calculate earned vacation based on staff category
        $user = Auth::user();
        $availableDays = 0;
        $earnedDays = 0;
        
        // Get available days from user record (should be stored in CRM)
        if ($user->available_days) {
            $availableDays = $user->available_days;
        }
        
        // Calculate earned days based on staff category
        if ($user->name == 'Hazem Alashi') {
            $earnedDays = (26/365) * $availableDays;
        } elseif (in_array($user->name, ['Ayman Hussein', 'Nidal Ahmad', 'Yasin Mohamad'])) {
            $earnedDays = (30/365) * $availableDays;
        } else {
            $earnedDays = (14/365) * $availableDays;
        }
        
        // Calculate overtime
        $overtime = max(0, $totalHours - 8);

        $onlineRequest = OnlineRequest::create([
            'type' => 'working_hours',
            'form_data' => json_encode(array_merge($request->all(), [
                'total_hours' => $totalHours,
                'earned_days' => $earnedDays,
                'overtime_hours' => $overtime
            ])),
            'status' => 'pending',
            'user_email' => Auth::user()->email,
        ]);

        if($onlineRequest) {
            // Update user's available days and overtime
            $user->available_days = $availableDays + $earnedDays;
            $user->overtime_hours = ($user->overtime_hours ?? 0) + $overtime;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Working hours submitted successfully!',
                'total_hours_today' => $totalHours,
                'earned_days' => $earnedDays,
                'overtime_hours' => $overtime
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to save working hours'
        ]);
    }

    /**
     * Submit Leave / Vacation
     */
    public function submitLeave(Request $request)
    {
        $request->validate([
            'department' => 'required',
            'leave_type' => 'required',
            'reason' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'substitute' => 'required|array', // Multiple selection
        ]);

        // Calculate days and hours between start and end
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $hoursDiff = $end->diffInHours($start);
        $days = floor($hoursDiff / 24);
        $hours = $hoursDiff % 24;

        // Check if user has enough balance
        $user = Auth::user();
        $availableBalance = $user->available_days ?? 0;
        
        if ($request->leave_type != 'unpaid' && $days > $availableBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance. Available: ' . $availableBalance . ' days'
            ]);
        }

        $onlineRequest = OnlineRequest::create([
            'type' => 'leave_request',
            'form_data' => json_encode(array_merge($request->all(), [
                'days' => $days,
                'hours' => $hours,
                'total_hours' => $hoursDiff
            ])),
            'status' => 'pending',
            'user_email' => Auth::user()->email,
        ]);

        if($onlineRequest) {
            // Notify supervisors and HR
            $supervisors = User::where('role', 'supervisor')->get();
            $hr = User::where('role', 'hr')->get();
            
            Notification::send($supervisors, new LeaveRequestNotification($onlineRequest));
            Notification::send($hr, new LeaveRequestNotification($onlineRequest));

            return response()->json([
                'success' => true,
                'message' => 'Leave request submitted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to save leave request'
        ]);
    }

    /**
     * Submit Overtime
     */
    public function submitOvertime(Request $request)
    {
        $request->validate([
            'overtime_datetime' => 'required',
            'overtime_hours' => 'required|numeric|min:1',
        ]);

        $onlineRequest = OnlineRequest::create([
            'type' => 'overtime',
            'form_data' => json_encode($request->all()),
            'status' => 'pending',
            'user_email' => Auth::user()->email,
        ]);

        if($onlineRequest) {
            // Notify supervisors and HR
            $supervisors = User::where('role', 'supervisor')->get();
            $hr = User::where('role', 'hr')->get();
            
            Notification::send($supervisors, new OvertimeRequestNotification($onlineRequest));
            Notification::send($hr, new OvertimeRequestNotification($onlineRequest));

            return response()->json([
                'success' => true,
                'message' => 'Overtime submitted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to save overtime request'
        ]);
    }

    /**
     * HR Report Generation
     */
    public function generateHrReport(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        
        // Get all working hours records for the month
        $records = OnlineRequest::where('type', 'working_hours')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();
        
        $report = [];
        $alerts = [];
        
        foreach ($records as $record) {
            $userEmail = $record->user_email;
            $user = User::where('email', $userEmail)->first();
            $data = $record->form_data;
            
            if (!isset($report[$userEmail])) {
                $report[$userEmail] = [
                    'name' => $user->name,
                    'total_hours' => 0,
                    'work_days' => [],
                    'leave_requests' => []
                ];
            }
            
            // Calculate monthly hours
            $report[$userEmail]['total_hours'] += $data['total_hours'] ?? 0;
            
            // Track work days
            $workDate = $data['work_date'] ?? null;
            if ($workDate) {
                $dayOfWeek = date('w', strtotime($workDate));
                $report[$userEmail]['work_days'][$workDate] = $dayOfWeek;
            }
            
            // Check for >180 hours per month
            if ($report[$userEmail]['total_hours'] > 180) {
                $alerts[] = [
                    'type' => 'excess_hours',
                    'message' => $user->name . ' exceeded 180 hours this month (' . $report[$userEmail]['total_hours'] . ' hours)',
                    'user' => $user->name,
                    'value' => $report[$userEmail]['total_hours']
                ];
            }
        }
        
        // Check for >6 days/week
        foreach ($report as $userEmail => $userData) {
            $daysByWeek = [];
            foreach ($userData['work_days'] as $date => $dayOfWeek) {
                $weekNumber = date('W', strtotime($date));
                if (!isset($daysByWeek[$weekNumber])) {
                    $daysByWeek[$weekNumber] = 0;
                }
                $daysByWeek[$weekNumber]++;
            }
            
            foreach ($daysByWeek as $week => $daysCount) {
                if ($daysCount > 6) {
                    $alerts[] = [
                        'type' => 'excess_days',
                        'message' => $userData['name'] . ' worked ' . $daysCount . ' days in week ' . $week,
                        'user' => $userData['name'],
                        'value' => $daysCount
                    ];
                }
            }
        }
        
        // Get leave requests for highlighting
        $leaveRecords = OnlineRequest::where('type', 'leave_request')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();
            
        foreach ($leaveRecords as $record) {
            $userEmail = $record->user_email;
            $user = User::where('email', $userEmail)->first();
            $data = $record->form_data;
            
            if (in_array($data['leave_type'], ['unpaid', 'sick', 'other'])) {
                $alerts[] = [
                    'type' => 'special_leave',
                    'message' => $user->name . ' took ' . $data['leave_type'] . ' leave (' . 
                                ($data['days'] ?? 0) . ' days, ' . ($data['hours'] ?? 0) . ' hours)',
                    'user' => $user->name,
                    'leave_type' => $data['leave_type']
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'report' => $report,
            'alerts' => $alerts
        ]);
    }
    
    /**
     * Approve/Reject Request (for supervisors and HR)
     */
    public function handleRequest(Request $request, $id)
    {
        $onlineRequest = OnlineRequest::findOrFail($id);
        $action = $request->action; // 'approve' or 'reject'
        $user = Auth::user();
        
        // Check if user has permission (supervisor or HR)
        if (!in_array($user->role, ['supervisor', 'hr'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }
        
        if ($action == 'approve') {
            $onlineRequest->status = 'approved';
            $onlineRequest->approved_by = $user->id;
            $onlineRequest->approved_at = now();
            
            // If HR is finalizing a leave request, update user's balance
            if ($user->role == 'hr' && $onlineRequest->type == 'leave_request') {
                $data = $onlineRequest->form_data;
                $requestUser = User::where('email', $onlineRequest->user_email)->first();
                
                if ($data['leave_type'] != 'unpaid') {
                    $requestUser->available_days = max(0, ($requestUser->available_days ?? 0) - $data['days']);
                    $requestUser->save();
                }
            }
        } else {
            $onlineRequest->status = 'rejected';
            $onlineRequest->rejected_by = $user->id;
            $onlineRequest->rejected_at = now();
            $onlineRequest->rejection_reason = $request->reason;
        }
        
        $onlineRequest->save();
        
        // TODO: Send notification to the requester
        
        return response()->json([
            'success' => true,
            'message' => 'Request ' . $action . 'd successfully'
        ]);
    }
}