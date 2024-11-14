<?php
$arrContextOptions= [
    'ssl' => [
        'cafile' => '/usr/local/share/ca-certificates/custom_root_ca.crt',
        'verify_peer'=> true,
        'verify_peer_name'=> true,
    ],
];
?>
<!DOCTYPE html>
<html lang="en" class="text-[11px] bg-white">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@lang('pdf_estimate_label') - {{ $estimate->estimate_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="tailwind-config">
        {
        theme: {
        }
      }
    </script>
    <style>
	@page {
		margin: 0.4in;
	}
    </style>
</head>

<body class="text-slate-800">
    <header id="header">
    @if ($estimate->status === 'DRAFT')
    	<span class="block fixed w-full top-0 text-3xl text-red-500 font-semibold text-center">@lang('pdf_draft_label')</span>
	@endif
	<section class="flex justify-between items-center">  
		<div>
		    @if ($logo)
		    <img class="h-24" src="data:image/png;base64,{{base64_encode(file_get_contents($logo,false,stream_context_create($arrContextOptions)))}}" alt="Company Logo">
		    @else
		    <h1 class="text-3xl"> {{ $estimate->customer->company->name }} </h1>
		    @endif
		</div>
		<div class="text-slate-500 text-right leading-tight">
		    {!! $company_address !!}
		</div>
	</section>
    	<hr class="my-8 border-b border-slate-300" />
    </header>
<section class="flex gap-16" id="information">
    <div>
	<h2 class="text-xl font-semibold text-slate-700">@lang('pdf_bill_to')</h2>
	{!! $billing_address !!}
    </div>
    <div>
	<h2 class="text-xl font-semibold text-slate-700">@lang('pdf_ship_to')</h2>
	{!! $shipping_address !!}
    </div>
    <div class="ml-auto">
	<h2 class="text-xl font-semibold text-slate-700">@lang('pdf_estimate_label')</h2>
	<div class="grid grid-cols-2 gap-x-3">
	    <span class="text-slate-600">@lang('pdf_estimate_number')</span>
	    <span class="text-right">{{ $estimate->estimate_number }}</span>
	    <span class="text-slate-600">@lang('pdf_estimate_date')</span>
	    <span class="text-right">{{ $estimate->formattedEstimateDate }}</span>
	    <span class="text-slate-600">@lang('pdf_estimate_expire_date')</span>
	    <span class='text-right'>{{ $estimate->formattedExpiryDate }}</span>
	</div>
    </div>
</section>
<section class="my-8 break-inside-avoid" id="notes">
    @if ($notes)
    <h2 class="text-slate-600 font-semibold">@lang('pdf_notes')</h2>
    <p>{!! $notes !!}</p>
    @endif
</section>
<section class="my-8 break-inside-avoid" id="items">
    @include('app.pdf.estimate.partials.table-tailwind')
</section>
<script type="text/javascript">
	const header = document.getElementById("header");
	const info = document.getElementById("information");
	const items = document.getElementById("items");
	const notes = document.getElementById("notes");
	if (header.clientHeight + info.clientHeight + items.clientHeight > 850) {
		const warning = document.createElement('p');
		warning.classList.add('text-slate-400');
		warning.innerHTML = "<br/> @lang('pdf_items_moved_label')";
		notes.appendChild(warning);
	}
</script>
</body>

</html>
