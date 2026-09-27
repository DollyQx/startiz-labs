@extends('layouts.client')

@section('title', 'My Notifications')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Account Notifications</h1>
                @if($unreadCount > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-600 text-white shadow-sm">
                        {{ $unreadCount }} Unread
                    </span>
                @endif
            </div>
            <p class="text-sm text-slate-500 mt-1">Updates on your project deliverables, invoices, support tickets, and quotations.</p>
        </div>

        @if($unreadCount > 0)
            <form action="{{ route('client.notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('client.notifications.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('unread') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                All Notifications
            </a>
            <a href="{{ route('client.notifications.index', ['unread' => 1]) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('unread') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                Unread Only ({{ $unreadCount }})
            </a>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm divide-y divide-slate-100 overflow-hidden">
        @if($notifications->isEmpty())
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900">No notifications</h3>
                <p class="text-xs text-slate-500 mt-1">There are no notifications for your account at this time.</p>
            </div>
        @else
            @foreach($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $data = $notification->data;
                    $category = $data['category'] ?? 'system';
                    $url = $data['url'] ?? null;
                @endphp
                <div class="p-5 flex items-start justify-between gap-4 transition-colors {{ $isUnread ? 'bg-blue-50/40 border-l-4 border-l-blue-600' : 'hover:bg-slate-50/60' }}">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5 {{ $isUnread ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-500' }}">
                            @if($category === 'quotation') Q
                            @elseif($category === 'invoice') I
                            @elseif($category === 'payment') $
                            @elseif($category === 'support_ticket') T
                            @elseif($category === 'change_request') C
                            @elseif($category === 'document') D
                            @else N @endif
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold text-slate-900">{{ $data['title'] ?? 'Notification' }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-slate-100 text-slate-600">
                                    {{ str_replace('_', ' ', $category) }}
                                </span>
                                @if($isUnread)
                                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-600 leading-relaxed">{{ $data['message'] ?? '' }}</p>
                            
                            <span class="text-[10px] text-slate-400 block pt-0.5">
                                {{ $notification->created_at->diffForHumans() }} ({{ $notification->created_at->format('M d, Y H:i') }})
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($url)
                            <a href="{{ $url }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                                View Details &rarr;
                            </a>
                        @endif

                        @if($isUnread)
                            <form action="{{ route('client.notifications.mark-read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100" title="Mark as read">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('client.notifications.mark-unread', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100" title="Mark as unread">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="p-4 border-t border-slate-200">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
