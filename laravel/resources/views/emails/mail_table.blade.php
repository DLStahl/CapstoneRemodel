<head>
@include('partials._mailCSS')
</head>
<body>

<table class="body-wrap">
	<tr>
		<td></td>
		<td class="container" width="600">
			<div class="content">
				<table class="main" width="100%" cellpadding="0" cellspacing="0">
				@foreach($dataRows as $dataRow)
					<tr>
						<td class="alert alert-osu">
							{{ $dataRow['heading'] }}
						</td>
					</tr>
					<tr>
						<td class="content-wrap">
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td class="">
										{{ $dataRow['body'] }}
									</td>
								</tr>
							</table>
						</td>
					</tr>
				@endforeach
				</table>
				<div class="footer">
					<table width="100%">
						<tr>
							<td class="aligncenter content-block">
								<img src="https://wexnermedical.osu.edu/-/media/images/wexnermedical/global/modules/global/header/osuwexmedctr.png?la=en&hash=E40538F4EC98B54A105B79EC8FA1BAB09709DDDC" height="40" alt="">
							</td>
						</tr>
					</table>
				</div>
			</div>
		</td>
	</tr>
</table>

</body>