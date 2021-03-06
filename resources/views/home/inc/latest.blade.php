<?php
if (!isset($cacheExpiration)) {
    $cacheExpiration = (int)config('settings.optimization.cache_expiration');
}
if (config('settings.listing.display_mode') == '.compact-view') {
	$colDescBox = 'col-sm-9';
	$colPriceBox = 'col-sm-3';
} else {
	$colDescBox = 'col-sm-7';
	$colPriceBox = 'col-sm-3';
}
$hideOnMobile = '';
if (isset($latestOptions, $latestOptions['hide_on_mobile']) and $latestOptions['hide_on_mobile'] == '1') {
	$hideOnMobile = ' hidden-sm';
}
?>
@if (isset($latest) and !empty($latest) and $latest->posts->count() > 0)
	@includeFirst([config('larapen.core.customizedViewPath') . 'home.inc.spacer', 'home.inc.spacer'], ['hideOnMobile' => $hideOnMobile])
	<div class="container{{ $hideOnMobile }}">
		<div class="col-xl-12 content-box layout-section">
			<div class="row row-featured row-featured-category">
				
				<div class="col-xl-12 box-title no-border">
					<div class="inner">
						<h2>
							<span class="title-3">{!! $latest->title !!}</span>
							<a href="{{ $latest->link }}" class="sell-your-item">
								{{ t('View more') }} <i class="icon-th-list"></i>
							</a>
						</h2>
					</div>
				</div>
				
				<div id="postsList" class="adds-wrapper noSideBar category-list">
					@foreach($latest->posts as $key => $post)
						@continue(empty($countries) or !$countries->has($post->country_code))
						@continue(empty($post->postType) or empty($post->city))
						<?php
							// Main Picture
							if ($post->pictures->count() > 0) {
								$postImg = imgUrl($post->pictures->get(0)->filename, 'medium');
							} else {
								$postImg = imgUrl(config('larapen.core.picture.default'), 'medium');
							}
							
							// Check parent
							if (empty($post->category->parent_id)) {
								$liveCatParentId = $post->category->id;
							} else {
								$liveCatParentId = $post->category->parent_id;
							}
							
							// Check translation
							$liveCatName = $post->category->name;
						?>
						<div class="item-list">
							@if (isset($post->latestPayment, $post->latestPayment->package) and !empty($post->latestPayment->package))
								@if ($post->latestPayment->package->ribbon != '')
									<div class="cornerRibbons {{ $post->latestPayment->package->ribbon }}">
										<a href="#"> {{ $post->latestPayment->package->short_name }}</a>
									</div>
								@endif
							@endif
							
							<div class="row">
								<div class="col-sm-2 no-padding photobox">
									<div class="add-image">
										<span class="photo-count"><i class="fa fa-camera"></i> {{ $post->pictures->count() }} </span>
										<a href="{{ \App\Helpers\UrlGen::post($post) }}">
											<img class="lazyload img-thumbnail no-margin" src="{{ $postImg }}" alt="{{ $post->title }}">
										</a>
									</div>
								</div>
								
								<div class="{{ $colDescBox }} add-desc-box">
									<div class="items-details">
										<h5 class="add-title">
											<a href="{{ \App\Helpers\UrlGen::post($post) }}">{{ \Illuminate\Support\Str::limit($post->title, 70) }} </a>
										</h5>
										
										<span class="info-row">
											<span class="add-type business-ads tooltipHere"
												  data-toggle="tooltip"
												  data-placement="right"
												  title="{{ $post->postType->name }}"
											>
												{{ strtoupper(mb_substr($post->postType->name, 0, 1)) }}
											</span>&nbsp;
											<span class="date"><i class="icon-clock"></i> {{ $post->created_at->ago() }} </span>
											@if (isset($liveCatParentId) and isset($liveCatName))
												<span class="category">
													<i class="icon-folder-circled"></i>&nbsp;
													<a href="{!! \App\Helpers\UrlGen::search(array_merge(request()->except('c'), ['c'=>$liveCatParentId])) !!}" class="info-link">
														{{ $liveCatName }}</a>
												</span>
											@endif
											<span class="item-location">
												<i class="icon-location-2"></i>&nbsp;
											<a href="{!! \App\Helpers\UrlGen::search(array_merge(request()->except(['l', 'location']), ['l'=>$post->city_id])) !!}"
											   class="info-link">{{ $post->city->name }}</a> {{ (isset($post->distance)) ? '- ' . round($post->distance, 2) . getDistanceUnit() : '' }}
											</span>
										</span>
									</div>
									
									@if (config('plugins.reviews.installed'))
										@if (view()->exists('reviews::ratings-list'))
											@include('reviews::ratings-list')
										@endif
									@endif
								
								</div>
								
								<div class="{{ $colPriceBox }} text-right price-box">
									<h4 class="item-price">
										@if (isset($post->category, $post->category->type))
											@if (!in_array($post->category->type, ['not-salable']))
												@if ($post->price > 0)
													{!! \App\Helpers\Number::money($post->price) !!}
												@else
													{!! \App\Helpers\Number::money('--') !!}
												@endif
											@endif
										@else
											{{ '--' }}
										@endif
									</h4>
									@if (isset($post->latestPayment, $post->latestPayment->package) and !empty($post->latestPayment->package))
										@if ($post->latestPayment->package->has_badge == 1)
											<a class="btn btn-danger btn-sm make-favorite">
												<i class="fa fa-certificate"></i>
												<span> {{ $post->latestPayment->package->short_name }} </span>
											</a>&nbsp;
										@endif
									@endif
									@if (isset($post->savedByLoggedUser) and $post->savedByLoggedUser->count() > 0)
										<a class="btn btn-success btn-sm make-favorite" id="{{ $post->id }}">
											<i class="fa fa-heart"></i><span> {{ t('Saved') }} </span>
										</a>
									@else
										<a class="btn btn-default btn-sm make-favorite" id="{{ $post->id }}">
											<i class="fa fa-heart"></i><span> {{ t('Save') }} </span>
										</a>
									@endif
								</div>
							</div>
						</div>
					@endforeach
			
					<div style="clear: both"></div>
					
					@if (isset($latestOptions) and isset($latestOptions['show_view_more_btn']) and $latestOptions['show_view_more_btn'] == '1')
						<div class="mb20 text-center">
							<a href="{{ \App\Helpers\UrlGen::search() }}" class="btn btn-default mt10">
								<i class="fa fa-arrow-circle-right"></i> {{ t('View more') }}
							</a>
						</div>
					@endif
				</div>
				
			</div>
		</div>
	</div>
@endif

@section('after_scripts')
    @parent
    <script>
		/* Default view (See in /js/script.js) */
		@if (isset($posts) and count($posts) > 0)
			@if (config('settings.listing.display_mode') == '.grid-view')
				gridView('.grid-view');
			@elseif (config('settings.listing.display_mode') == '.list-view')
				listView('.list-view');
			@elseif (config('settings.listing.display_mode') == '.compact-view')
				compactView('.compact-view');
			@else
				gridView('.grid-view');
			@endif
		@else
			listView('.list-view');
		@endif
		/* Save the Search page display mode */
		var listingDisplayMode = readCookie('listing_display_mode');
		if (!listingDisplayMode) {
			createCookie('listing_display_mode', '{{ config('settings.listing.display_mode', '.grid-view') }}', 7);
		}
		
		/* Favorites Translation */
		var lang = {
			labelSavePostSave: "{!! t('Save ad') !!}",
			labelSavePostRemove: "{!! t('Remove favorite') !!}",
			loginToSavePost: "{!! t('Please log in to save the Ads') !!}",
			loginToSaveSearch: "{!! t('Please log in to save your search') !!}",
			confirmationSavePost: "{!! t('Post saved in favorites successfully') !!}",
			confirmationRemoveSavePost: "{!! t('Post deleted from favorites successfully') !!}",
			confirmationSaveSearch: "{!! t('Search saved successfully') !!}",
			confirmationRemoveSaveSearch: "{!! t('Search deleted successfully') !!}"
		};
    </script>
@endsection