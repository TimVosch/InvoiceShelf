<?php
$tableCols = '';
$tableCols = $tableCols . ' 2%';
$tableCols = $tableCols . ' minmax(min-content, 45%)';
foreach ($customFields as $field) {
    $tableCols = $tableCols . ' auto';
}
$tableCols = $tableCols . ' auto';
$tableCols = $tableCols . ' auto';
if ($estimate->discount_per_item == 'YES')
    $tableCols = $tableCols . ' auto';
$tableCols = $tableCols . ' auto';
?>
<section>
    <div class="grid gap-x-10 gap-y-2 items-center" style="grid-template-columns: {{$tableCols}};">
        <hr class="col-span-full my-3" />
        <!-- Header -->
        <span class="font-semibold">#</span>
        <span class="font-semibold">@lang('pdf_items_label')</span>
        @foreach($customFields as $field)
        <span class="font-semibold">{{ $field->label }}</span>
        @endforeach
        <span class="font-semibold text-right">@lang('pdf_quantity_label')</span>
        <span class="font-semibold text-right">@lang('pdf_price_label')</span>
        @if($estimate->discount_per_item === 'YES')
        <span class="font-semibold text-right">@lang('pdf_discount_label')</span>
        @endif
        <span class="font-semibold text-right">@lang('pdf_amount_label')</span>

        <!-- hr -->
        <hr class="col-span-full my-3" />

        <!-- Items -->
        @php
        $index = 1
        @endphp
        @foreach ($estimate->items as $item)
        <span class="">{{ $index }}</span>
        <span class="">
            {{ $item->name }}
            @if ($item->description)
	    <br/>
            <span class="text-sm text-slate-500">{{ nl2br(htmlspecialchars($item->description)) }}</span>
            @endif
        </span>
        @foreach ($customFields as $field)
        <span class="text-right">{{ $field->value }}</span>
        @endforeach
        <span class="text-right">{{$item->quantity}} @if($item->unit_name) {{$item->unit_name}} @endif</span>
        <span class="text-right">{!! format_money_pdf($item->price, $estimate->customer->currency) !!}</span>
        @if($estimate->discount_per_item == 'YES')
        <span class="text-right">
            @if($item->discount_type === 'fixed')
            {!! format_money_pdf($item->discount_val, $estimate->customer->currency) !!}
            @endif
            @if($item->discount_type === 'percentage')
            {{$item->discount}}%
            @endif
        </span>
        @endif
        <span class="text-right">{!! format_money_pdf($item->total, $estimate->customer->currency) !!}</span>
        @php
        $index += 1
        @endphp
        @endforeach

        <!-- hr -->
        <hr class="col-span-full my-3" />
    </div>

    <!-- Totals -->
    <div class="flex justify-end break-inside-avoid">
        <div class="grid gap-y-2 gap-x-6" style="grid-template-columns: max-content max-content;">
            <span class="text-slate-600">@lang('pdf_subtotal')</span>
            <span class="text-right">{!! format_money_pdf($estimate->sub_total, $estimate->customer->currency) !!}</span>
            @if($estimate->discount > 0)
            @if ($estimate->discount_per_item == 'NO')
            <span class="text-slate-600">
                @if($estimate->discount_type === 'fixed')
                @lang('pdf_discount_label')
                @endif
                @if($estimate->discount_type === 'percentage')
                @lang('pdf_discount_label') ({{$estimate->discount}}%)
                @endif
            </span>
            <span class="text-right">
                @if($estimate->discount_type === 'fixed')
                {!! format_money_pdf($estimate->discount_val, $estimate->customer->currency) !!}
                @endif
                @if($estimate->discount_type === 'percentage')
                {!! format_money_pdf($estimate->discount_val, $estimate->customer->currency) !!}
                @endif
            </span>
            @endif
            @endif

            @if($estimate->tax_per_item == 'YES')
            @foreach ($taxes as $tax)
            <span class="text-slate-600">{{ $tax->name.' ('.$tax->percent.'%)' }}</span>
            <span class="text-right">
                {!! format_money_pdf($tax->amount, $estimate->customer->currency) !!}
            </span>
            @endforeach
            @else
            @foreach ($estimate->taxes as $tax)
            <span class="text-slate-600">{{ $tax->name.' ('.$tax->percent.'%)' }}</span>
            <span class="text-right">
                {!! format_money_pdf($tax->amount, $estimate->customer->currency) !!}
            </span>
            @endforeach
            @endif

            <span class="text-orange-600">@lang('pdf_total')</span>
            <span class="text-orange-700 text-right">{!! format_money_pdf($estimate->total, $estimate->customer->currency) !!}</span>
        </div>
    </div>
</section>
