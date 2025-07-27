@extends('isotope::master')

@section('title', __('shopboss::shopboss.purchaseReport'))

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="#">
                <div class="row bg-light p-3">
                    <div class="col-md mb-1">
                        <label class="form-label">{{ __('shopboss::shopboss.fromDate') }} :</label>
                        <input name="from" type="date" class="form-control form-control-sm" required=""
                            value="{{ $from ? date('Y-m-d',strtotime($from)) : date('Y-m-d') }}">
                    </div>
                    <div class="col-md mb-1">
                        <label class="form-label">{{ __('shopboss::shopboss.toDate') }} :</label>
                        <input name="to" type="date" class="form-control form-control-sm" required=""
                            value="{{ $to ? date('Y-m-d',strtotime($to)): date('Y-m-d') }}">
                    </div>

                    <div class="col-md mb-1 mt-5">
                        <button type="submit" title="{{ __('shopboss::shopboss.loadData') }}" class="btn btn-success btn-sm fw-bold mt-3 mr-2"
                            name="submit" value="load">
                            <i class="fa-solid fa-search fs-4 me-2"></i>
                            {{ __('shopboss::shopboss.load') }}
                        </button>
                        @if (count($data) > 0)
                            <a href="/purchases-report?from={{ $from }}&to={{ $to }}&submit=print" title="{{ __('shopboss::shopboss.printData') }}" class="btn btn-primary btn-sm fw-bold mt-3"
                                target="_blanck">
                                <i class="fa-solid fa-print fs-4 me-2"></i>
                                {{ __('shopboss::shopboss.print') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if (count($data) > 0)
                @include('shopboss::reports.purchases.plan')
            @else
                <table class="table table-border mt-5">
                    <tr class="bg-secondary">
                        <th class="text-center text-danger fw-bold">{{ __('shopboss::shopboss.noDataFound') }}</th>
                    </tr>
                </table>
            @endif
        </div>
    </div>

@endsection