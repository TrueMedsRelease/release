<form id="show_packagings_form" name="show_packagings_form" method="POST">
    <div class="adm-dual adm-dual--three">
        <div class="adm-dual__list">
            <h3 class="discounts__label">{{__('text.admin_packagings_show_all_products_title')}}</h3>
            <select class="input" name="all_products_field" id="all_products_field" size="30" onchange="loadProductPackaging(this.value)">
                @foreach ($all_products_info as $cur_product_info)
                    <option value="{{ $cur_product_info['id'] }}">{{ $cur_product_info['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="adm-dual__list">
            <h3 class="discounts__label">{{__('text.admin_packagings_show_not_showed_packagings_title')}}</h3>
            <select class="input" name="not_showed_packagings_field" id="not_showed_packagings_field" size="30" multiple >
                @if(count($not_showed_packagings) > 0)
                    @foreach ($not_showed_packagings as $packaging)
                        <option value="{{ $packaging->id }}">{{ $packaging->dosage }} x {{ $packaging->num }} {{ $packaging->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="adm-dual__controls">
            <span class="right_icon" id="add_button" title="{{ __('text.admin_products_show_add_to_list_of_showed_products_text') }}" onclick="addPackagngInShowed()" role="button"></span>
            <span class="left_icon" id="delete_button" title="{{ __('text.admin_products_show_delete_from_list_of_showed_products_text') }}" onclick="deletePackagngFromShowed()" role="button"></span>
        </div>
        <div class="adm-dual__list">
            <h3 class="discounts__label">{{__('text.admin_packagings_show_showed_packagings_title')}}</h3>
            <select class="input" name="showed_packagings_field" id="showed_packagings_field" size="30" multiple >
                @if(count($showed_packagings) > 0)
                    @foreach ($showed_packagings as $packaging)
                        <option value="{{ $packaging->id }}">{{ $packaging->dosage }} x {{ $packaging->num }} {{ $packaging->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="adm-dual__controls">
            <span class="up_icon" id="up_button" title='{{ __('text.admin_packagings_show_up_pos_text') }}' onclick="packagingUpInSort()" role="button"></span>
            <span class="down_icon" id="down_button" title='{{ __('text.admin_packagings_show_down_pos_text') }}' onclick="packagingDownInSort()" role="button"></span>
        </div>
    </div>
</form>