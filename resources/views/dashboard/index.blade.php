@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold">Dashboard</h2>
    <p class="text-muted">Overview of your email campaign performance</p>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Total Subscribers</p>
                    <h3 class="fw-bold mb-0">{{ number_format($totalSubscribers) }}</h3>
                    <small class="text-success"><i class="bx bx-up-arrow-alt"></i> +12.5% from last month</small>
                </div>
                <div class="stat-icon bg-light text-primary">
                    <i class="bx bx-user"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Total Campaigns</p>
                    <h3 class="fw-bold mb-0">{{ $totalCampaigns }}</h3>
                    <small class="text-success"><i class="bx bx-plus"></i> +3 new this month</small>
                </div>
                <div class="stat-icon bg-light text-info">
                    <i class="bx bx-send"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Emails Sent</p>
                    <h3 class="fw-bold mb-0">{{ $totalSent }}</h3>
                    <small class="text-success"><i class="bx bx-line-chart"></i> +8.2% growth</small>
                </div>
                <div class="stat-icon bg-light text-success">
                    <i class="bx bx-envelope"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-4">Email Performance</h5>
                <canvas id="performanceChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-4">Quick Actions</h5>
                <div class="d-grid gap-3">
                    <a href="{{ route('campaigns.create') }}" class="btn btn-outline-primary text-start p-3">
                        <i class="bx bx-plus-circle me-2"></i> Create New Campaign
                    </a>
                    <a href="{{ route('subscribers.create') }}" class="btn btn-outline-info text-start p-3">
                        <i class="bx bx-user-plus me-2"></i> Add New Subscriber
                    </a>
                    <a href="{{ route('settings.smtp.index') }}" class="btn btn-outline-secondary text-start p-3">
                        <i class="bx bx-cog me-2"></i> Configure SMTP
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection