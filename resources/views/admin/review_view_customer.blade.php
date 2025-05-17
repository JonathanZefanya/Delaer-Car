@extends('admin.app_admin')
@section('admin_content')
    <h1 class="h3 mb-3 text-gray-800">{{ CUSTOMER_REVIEWS }}</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>{{ SERIAL }}</th>
                        <th>{{LISTING_FEATURED_PHOTO }}</th>
                        <th>{{LISTING_NAME }}</th>
                        <th>{{ CUSTOMER_NAME }}</th>
                        <th class="w_200">{{ RATING }}</th>
                        <th class="w_200">{{ REVIEW }}</th>
                        <th>{{ ACTION }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @php $i=0; @endphp
                        @foreach($reviews as $row)
                            @php
                                $single_listing_item = \App\Models\Listing::where('id', $row->listing_id)->first();
                                $customer_detail = \App\Models\User::where('id',$row->agent_id)->first();
                            @endphp
                            @php $i++; @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if(optional($single_listing_item)->featured_photo)
                                        <img src="{{ asset('uploads/listing_photos/'.$single_listing_item->featured_photo) }}" class="w_100">
                                    @else
                                        <img src="{{ asset('uploads/listing_photos/default-avatar.jpg') }}" class="w_100">
                                    @endif
                                </td>
                                <td>
                                    @if($single_listing_item)
                                        {{ $single_listing_item->listing_name }} <br>
                                        <a href="{{ route('front_listing_detail', $single_listing_item->listing_slug) }}" class="badge badge-success" target="_blank">{{ SEE_DETAIL }}</a>
                                    @else
                                        <span class="text-danger">Listing tidak ditemukan</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $customer_detail->name }}
                                    <a href="{{ route('admin_customer_detail',$customer_detail->id) }}" class="badge badge-success" target="_blank">{{ SEE_DETAIL }}</a>
                                </td>
                                <td>
                                    <div class="my-review">
                                        @if($row->rating == 5)
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        @elseif($row->rating == 4)
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        @elseif($row->rating == 3)
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        @elseif($row->rating == 2)
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        @elseif($row->rating == 1)
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    {!! clean(nl2br($row->review)) !!}
                                </td>
                                <td>
                                    <a href="{{ route('admin_delete_customer_review',$row->id) }}" class="btn btn-danger btn-sm" onClick="return confirm('{{ ARE_YOU_SURE }}');"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>




@endsection
