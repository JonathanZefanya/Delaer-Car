@extends('front.app_front')

@section('content')

<div class="search-section" style="background-image:url('{{ asset('uploads/site_photos/'.$page_home_items->search_background) }}');">
	<div class="bg"></div>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1>{{ $page_home_items->search_heading }}</h1>
				<p>
					{{ $page_home_items->search_text }}
				</p>
				<div class="box">
					<form action="{{ url('search-listing') }}" method="POST">
                        @csrf
						<div class="input-group input-box mb-3">
							<input type="text" class="form-control" placeholder="{{ FIND_ANYTHING }}" name="text">
							<select name="location[]" class="form-control select2">
								<option value="">{{ SELECT_LOCATION }}</option>
								@foreach($listing_locations as $row)
									<option value="{{ $row->id }}">{{ $row->listing_location_name }}</option>
								@endforeach
							</select>
							<select name="brand[]" class="form-control select2">
								<option value="">{{ SELECT_BRAND }}</option>
								@foreach($listing_brands as $row)
									<option value="{{ $row->id }}">{{ $row->listing_brand_name }}</option>
								@endforeach
							</select>
							<select name="listing_type" class="form-control select2">
								<option value="">{{ SELECT_TYPE }}</option>
								<option value="New Car">{{ NEW_CAR }}</option>
								<option value="Used Car">{{ USED_CAR }}</option>
							</select>
							<div class="input-group-append">
								<button type="submit"><i class="fa fa-search"></i> {{ SEARCH }}</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


@if($adv_home_data->above_brand_status == 'Show')
<div class="ad-section">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-sm-12 wow fadeInUp">
				<div class="inner">
					@if($adv_home_data->above_brand_1_url == '')
						<img src="{{ asset('uploads/advertisements/'.$adv_home_data->above_brand_1) }}" alt="">
					@else
						<a href="{{ $adv_home_data->above_brand_1_url }}" target="_blank"><img src="{{ asset('uploads/advertisements/'.$adv_home_data->above_brand_1) }}" alt=""></a>
					@endif
				</div>
			</div>
			<div class="col-md-6 col-sm-12 wow fadeInUp">
				<div class="inner">
					@if($adv_home_data->above_brand_2_url == '')
						<img src="{{ asset('uploads/advertisements/'.$adv_home_data->above_brand_2) }}" alt="">
					@else
						<a href="{{ $adv_home_data->above_brand_2_url }}" target="_blank"><img src="{{ asset('uploads/advertisements/'.$adv_home_data->above_brand_2) }}" alt=""></a>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endif


@if($page_home_items->brand_status == 'Show')
<div class="popular-city">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="heading">
					<h2>{{ $page_home_items->brand_heading }}</h2>
					<h3>{{ $page_home_items->brand_subheading }}</h3>
				</div>
			</div>
		</div>
		<div class="row">
			@php $i=0; @endphp
			@foreach($orderwise_listing_brands as $row)
                @php $i++; @endphp
                @if($i>$page_home_items->brand_total)
                    @break;
                @endif
				@if($row->total == '')
        			@php $row->total = 0; @endphp
        		@endif
				<div class="col-lg-3 col-md-6 col-sm-6 wow fadeInUp">
					<div class="popular-city-item effect-item">
						<div class="photo image-effect">
							<img src="{{ asset('uploads/listing_brand_photos/'.$row->listing_brand_photo) }}" alt="">
						</div>				
						<div class="text">
							<h4>{{ $row->listing_brand_name }}</h4>
							@php
                                $qty = 0;
                                $brandListings = App\Models\Listing::where('listing_brand_id', $row->id)->where('listing_status','Active')->get();
                                foreach ($brandListings as $key => $brandListing) {
                                    if($brandListing->user_id != 0){
                                        $activePackage = App\Models\PackagePurchase::where('user_id',$brandListing->user_id)->where('currently_active',1)->first();
                                        if($activePackage->package_end_date >= date('Y-m-d')){
                                            $qty += 1;
                                        }
                                    }else{
                                        $qty += 1;
                                    }
                                }
                            @endphp
							<p>{{ $qty }} {{ ITEMS }}</p>
						</div>
						<a href="{{ route('front_listing_brand_detail',$row->listing_brand_slug) }}"></a>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>
@endif


@if($adv_home_data->above_featured_listing_status == 'Show')
<div class="ad-section">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-sm-12 wow fadeInUp">
				<div class="inner">
					@if($adv_home_data->above_featured_listing_1_url == '')
						<img src="{{ asset('uploads/advertisements/'.$adv_home_data->above_featured_listing_1) }}" alt="">
					@else
						<a href="{{ $adv_home_data->above_featured_listing_1_url }}" target="_blank"><img src="{{ asset('uploads/advertisements/'.$adv_home_data->above_featured_listing_1) }}" alt=""></a>
					@endif
				</div>
			</div>
			<div class="col-md-6 col-sm-12 wow fadeInUp">
				<div class="inner">
					@if($adv_home_data->above_featured_listing_2_url == '')
						<img src="{{ asset('uploads/advertisements/'.$adv_home_data->above_featured_listing_2) }}" alt="">
					@else
						<a href="{{ $adv_home_data->above_featured_listing_2_url }}" target="_blank"><img src="{{ asset('uploads/advertisements/'.$adv_home_data->above_featured_listing_2) }}" alt=""></a>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endif


@if($page_home_items->video_status == 'Show')
<div class="home-video" style="background-image: url({{ asset('uploads/site_photos/'.$page_home_items->video_background) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $page_home_items->video_heading }}</h2>
                <p>
					{!! clean(nl2br($page_home_items->video_text)) !!}
                </p>
                <div class="video-section">
                    <a class="video-button" href="http://www.youtube.com/watch?v={{ $page_home_items->video_youtube_id }}"><i class="far fa-play-circle"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif


@if($page_home_items->listing_status == 'Show')
<div class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="heading">
					<h2>{{ $page_home_items->listing_heading }}</h2>
					<h3>{{ $page_home_items->listing_subheading }}</h3>
				</div>
			</div>
		</div>
		<div class="row">
			@php
			$arr_max = array();
			for($j=0;$j<$page_home_items->listing_total;$j++) {
				$arr_max[] = 3*$j+1;
			}
			@endphp
			@php $i=0; @endphp
			@foreach($listings as $row)
			@php $i++; @endphp
			@if($i>$page_home_items->listing_total)
				@break;
			@endif
			@if($i%3==0)
				@php $fade_val = 'fadeInRight' @endphp
			@elseif(in_array($i,$arr_max))
				@php $fade_val = 'fadeInLeft' @endphp
			@else
				@php $fade_val = 'fadeInUp' @endphp
			@endif
			<div class="col-lg-4 col-md-6 col-sm-12 wow {{ $fade_val }}">
				<div class="listing-item effect-item">
					<div class="photo image-effect">
						<a href="{{ route('front_listing_detail',$row->listing_slug) }}"><img src="{{ asset('uploads/listing_featured_photos/'.$row->listing_featured_photo) }}" alt=""></a>
						<div class="brand">
							<a href="{{ route('front_listing_brand_detail',$row->rListingBrand->listing_brand_slug) }}">{{ $row->rListingBrand->listing_brand_name }}</a>
						</div>
						<div class="wishlist">
							<a href="{{ route('front_add_wishlist',$row->id) }}"><i class="fas fa-heart"></i></a>
						</div>
						<div class="featured-text">{{ FEATURED }}</div>
					</div>
					<div class="text">

						<div class="type-price">
							<div class="type">
								@if($row->listing_type == 'New Car')
                                <div class="inner-new">
                                    {{ $row->listing_type }}
                                </div>
                                @else
                                <div class="inner-used">
                                    {{ $row->listing_type }}
                                </div>
                                @endif
							</div>
							<div class="price">
								@if(!session()->get('currency_symbol'))
									${{ number_format($row->listing_price) }}
								@else
									{{ session()->get('currency_symbol') }}{{ number_format($row->listing_price*session()->get('currency_value')) }}
								@endif
							</div>
						</div>

						<h3><a href="{{ route('front_listing_detail',$row->listing_slug) }}">{{ $row->listing_name }}</a></h3>
						<div class="location">
							<i class="fas fa-map-marker-alt"></i> {{ $row->rListingLocation->listing_location_name }}
						</div>

						@php
							$count=0;
							$total_number = 0;
							$overall_rating = 0;
							$reviews = \App\Models\Review::where('listing_id',$row->id)->get();
						@endphp

						@if($reviews->isEmpty())

						@else

						@foreach($reviews as $item)
							@php
								$count++;
								$total_number = $total_number + $item->rating;
							@endphp
						@endforeach

						@php
							$overall_rating = $total_number/$count;
						@endphp

						@if($overall_rating>0 && $overall_rating<=1)
							@php $overall_rating = 1; @endphp

						@elseif($overall_rating>1 && $overall_rating<=1.5)
							@php $overall_rating = 1.5; @endphp

						@elseif($overall_rating>1.5 && $overall_rating<=2)
							@php $overall_rating = 2; @endphp

						@elseif($overall_rating>2 && $overall_rating<=2.5)
							@php $overall_rating = 2.5; @endphp

						@elseif($overall_rating>2.5 && $overall_rating<=3)
							@php $overall_rating = 3; @endphp

						@elseif($overall_rating>3 && $overall_rating<=3.5)
							@php $overall_rating = 3.5; @endphp

						@elseif($overall_rating>3.5 && $overall_rating<=4)
							@php $overall_rating = 4; @endphp

						@elseif($overall_rating>4 && $overall_rating<=4.5)
							@php $overall_rating = 4.5; @endphp

						@elseif($overall_rating>4.5 && $overall_rating<=5)
							@php $overall_rating = 5; @endphp

						@endif

						@endif

						<div class="review">
							@if($overall_rating == 5)
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
							@elseif($overall_rating == 4.5)
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star-half-alt"></i>
							@elseif($overall_rating == 4)
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="far fa-star"></i>
							@elseif($overall_rating == 3.5)
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star-half-alt"></i>
								<i class="far fa-star"></i>
							@elseif($overall_rating == 3)
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
							@elseif($overall_rating == 2.5)
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star-half-alt"></i>
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
							@elseif($overall_rating == 2)
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
							@elseif($overall_rating == 1.5)
								<i class="fas fa-star"></i>
								<i class="fas fa-star-half-alt"></i>
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
							@elseif($overall_rating == 1)
								<i class="fas fa-star"></i>
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
							@elseif($overall_rating == 0)
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
								<i class="far fa-star"></i>
							@endif
							<span>({{ $count }} {{ REVIEWS }})</span>
						</div>

					</div>
				</div>
			</div>
			@endforeach				
		</div>
	</div>
</div>
@endif


@if($page_home_items->testimonial_status == 'Show')
<div class="testimonial" style="background-image:url('{{ asset('uploads/site_photos/'.$page_home_items->testimonial_background) }}');">
    <div class="testimonial-bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="heading">
                    <h2>{{ $page_home_items->testimonial_heading }}</h2>
					<h3>{{ $page_home_items->testimonial_subheading }}</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="testimonial-carousel owl-carousel">
					@foreach($testimonials as $row)
					<div class="testimonial-item wow fadeInUp">
                        <div class="photo">
                            <img src="{{ asset('uploads/testimonials/'.$row->photo) }}" alt="">
                        </div>
                        <div class="text">
                            <p>
                                {!! clean(nl2br($row->comment)) !!}
                            </p>
                            <h3>{{ $row->name }}</h3>
                            <h4>{{ $row->designation }}</h4>
                        </div>
                    </div>
					@endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif



@if($adv_home_data->above_location_status == 'Show')
<div class="ad-section">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-sm-12 wow fadeInUp">
				<div class="inner">
					@if($adv_home_data->above_location_1_url == '')
						<img src="{{ asset('uploads/advertisements/'.$adv_home_data->above_location_1) }}" alt="">
					@else
						<a href="{{ $adv_home_data->above_location_1_url }}" target="_blank"><img src="{{ asset('uploads/advertisements/'.$adv_home_data->above_location_1) }}" alt=""></a>
					@endif
				</div>
			</div>
			<div class="col-md-6 col-sm-12 wow fadeInUp">
				<div class="inner">
					@if($adv_home_data->above_location_2_url == '')
						<img src="{{ asset('uploads/advertisements/'.$adv_home_data->above_location_2) }}" alt="">
					@else
						<a href="{{ $adv_home_data->above_location_2_url }}" target="_blank"><img src="{{ asset('uploads/advertisements/'.$adv_home_data->above_location_2) }}" alt=""></a>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endif


@if($page_home_items->location_status == 'Show')
<div class="popular-city">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="heading">
					<h2>{{ $page_home_items->location_heading }}</h2>
					<h3>{{ $page_home_items->location_subheading }}</h3>
				</div>
			</div>
		</div>
		<div class="row">
			
			@php $i=0; @endphp
			@foreach($orderwise_listing_locations as $row)
				@php $i++; @endphp
				@if($i>$page_home_items->location_total)
					@break;
				@endif
				@if($row->total == '')
					@php $row->total = 0; @endphp
				@endif
				<div class="col-lg-3 col-md-6 col-sm-6 wow fadeInUp">
					<div class="popular-city-item effect-item">
						<div class="photo image-effect">
							<img src="{{ asset('uploads/listing_location_photos/'.$row->listing_location_photo) }}" alt="">
						</div>				
						<div class="text">
							<h4>{{ $row->listing_location_name }}</h4>
							@php
								$qty = 0;
								$locationListings = App\Models\Listing::where('listing_location_id', $row->id)->where('listing_status','Active')->get();
								foreach ($locationListings as $key => $brandListing) {
									if($brandListing->user_id != 0){
										$activePackage = App\Models\PackagePurchase::where('user_id',$brandListing->user_id)->where('currently_active',1)->first();
										if($activePackage->package_end_date >= date('Y-m-d')){
											$qty += 1;
										}
									}else{
										$qty += 1;
									}
								}
							@endphp
							<p>{{ $qty }} {{ LISTINGS }}</p>
						</div>
						<a href="{{ route('front_listing_location_detail',$row->listing_location_slug) }}"></a>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>
@endif

@endsection
