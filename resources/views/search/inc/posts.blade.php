<?php
if (!isset($cacheExpiration)) {
    $cacheExpiration = (int)config('settings.optimization.cache_expiration');
}
?>
@if (isset($posts) and $posts->total() > 0)
	<?php
	if (!isset($cats)) {
		$cats = collect([]);
	}

	foreach($posts->items() as $key => $post):
		if (empty($countries) or !$countries->has($post->country_code)) continue;
		if (empty($post->postType) or empty($post->city)) continue;
		
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
		if ($cats->has($liveCatParentId)) {
			$liveCatName = $cats->get($liveCatParentId)->name;
		} else {
			$liveCatName = $post->category->name;
		}
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
			<div class="col-md-2 no-padding photobox">
				<div class="add-image">
					<span class="photo-count"><i class="fa fa-camera"></i> {{ $post->pictures->count() }} </span>
					<a href="{{ \App\Helpers\UrlGen::post($post) }}">
						<img class="lazyload img-thumbnail no-margin" src="{{ $postImg }}" alt="{{ $post->title }}">
					</a>
				</div>
			</div>
	
			<div class="col-md-7 add-desc-box">
				<div class="items-details">
					<h5 class="add-title">
						<a href="{{ \App\Helpers\UrlGen::post($post) }}">{{ \Illuminate\Support\Str::limit($post->title, 70) }} </a>
					</h5>
					
					<span class="info-row">
						<span class="add-type business-ads tooltipHere" data-toggle="tooltip" data-placement="right" title="{{ $post->postType->name }}">
							{{ strtoupper(mb_substr($post->postType->name, 0, 1)) }}
						</span>&nbsp;
						<span class="date"><i class="icon-clock"></i> {{ $post->created_at->ago() }} </span>
						@if (isset($liveCatParentId) and isset($liveCatName))
							<span class="category">
								<i class="icon-folder-circled"></i>&nbsp;
								<a href="{!! \App\Helpers\UrlGen::search(array_merge(request()->except('c'), ['c'=>$liveCatParentId])) !!}" class="info-link">
									{{ $liveCatName }}
								</a>
							</span>
						@endif
						<span class="item-location">
							<i class="icon-location-2"></i>&nbsp;
							<a href="{!! \App\Helpers\UrlGen::search(array_merge(request()->except(['l', 'location']), ['l'=>$post->city_id])) !!}" class="info-link">
								{{ $post->city->name }}
							</a> {{ (isset($post->distance)) ? '- ' . round($post->distance, 2) . getDistanceUnit() : '' }}
						</span>
					</span>
				</div>
	
				@if (config('plugins.reviews.installed'))
					@if (view()->exists('reviews::ratings-list'))
						@include('reviews::ratings-list')
					@endif
				@endif
				
			</div>
	
			<div class="col-md-3 text-right price-box">
				<h4 class="item-price">
					@if (isset($post->category->type))
						@if (!in_array($post->category->type, ['not-salable']))
							@if ($post->price > 0)
								{!! \App\Helpers\Number::money($post->price) !!}
							@else
								{!! \App\Helpers\Number::money(' --') !!}
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
	<?php endforeach; ?>
@else
	<div class="p-4" style="width: 100%;">
		{{ t('no_result_refine_your_search') }}
	</div>
@endif

@section('after_scripts')
	@parent
	<script>
		/* Default view (See in /js/script.js) */
		@if ($count->get('all') > 0)
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
