@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Dashboard Card -->
            <div class="card shadow-lg border-0">
                <div class="card-header">
                    <h4>Dashboard</h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">

                        <!-- Total Articles Card -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm rounded h-40">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-book"></i> Total Articles</h5>
                                    <h1>{{ $totalArticles }}</h1>
                                </div>
                            </div>
                        </div>

                        <!-- Article Progress Card -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm rounded  h-40">
                                <div class="card-body">
                                    <h5 class="card-title "><i class="fas fa-clock"></i> Article Progress</h5>
                                    <div class="progress rounded-pill mb-2" style="height: 10px;">
                                        <div class="progress-bar bg-dark" role="progressbar" style="width: {{ $progressPercentage }}%;">
                                            {{ round($progressPercentage) }}%
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <small class="d-block ">Pending: {{ $pendingArticles }}</small>
                                        </div>
                                        <div class="col">
                                            <small class="d-block ">Confirmed: {{ $confirmedArticles }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Categories Card -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm rounded  h-40">
                                <div class="card-body">
                                    <h5 class="card-title "><i class="fas fa-tags"></i> Categories</h5>
                                    <h1>{{ $totalCategories }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        @foreach($categories as $category)
                            <div class="col-sm text-center">
                                <div class="card shadow-sm rounded  h-40">
                                    <div class="card-body ">
                                        <h5 class="card-title">{{ $category->name }}</h5>
                                        <h1>{{ $category->articles_count }}</h1>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Owner-only Section -->
                    @if(auth()->user()->role === 'owner')
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <p>Owner-specific data and options go here.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
