@extends('base-pdf')

@section('body')
	<table class="table">
		<tbody>
			<tr>
				<th>Pet Name:</th>
				<td class="text-right">{{ $information['name'] }}</td>
			</tr>
			<tr>
				<th>Type:</th>
				<td class="text-right">{{ $information['type'] }}</td>
			</tr>
			<tr>
				<th>Birth Year:</th>
				<td class="text-right">{{ $information['birth_year'] }}</td>
			</tr>
			<tr>
				<th>Description:</th>
				<td class="text-right">
					@if($information['description']) {{ $information['description'] }} @else N/A @endif
				</td>
			</tr>
		</tbody>
	</table>


	@for($counter = 0; $counter < count($pet_appointments); $counter++)
		<div class="py-5 border-top border-bottom">
			<h5>
				<strong>Appointment Date:</strong>
				{{ $pet_appointments[$counter]['appointment_info']['information']['appointment_date_time'] }}
			</h5>
			<table class="table">
				<tbody>
					<tr>
						<th>Reason:</th>
						<td class="text-right">{!! $pet_appointments[$counter]['information']['reason'] !!}</td>
					</tr>
					<tr>
						<th>Findings:</th>
						<td class="text-right">{!! $pet_appointments[$counter]['information']['findings'] !!}</td>
					</tr>
					<tr>
						<th>Prescription:</th>
						<td>{!! $pet_appointments[$counter]['information']['prescription'] !!}</td>
					</tr>
					<tr>
						<th>Diseases Found</th>
						<td>
							<ul>
								@for($disease_counter = 0; $disease_counter < count($pet_appointments[$counter]['disease_findings']); $disease_counter++)

									<li>
										{{ $pet_appointments[$counter]['disease_findings'][$disease_counter]['disease']['disease_name'] }}
									</li>

								@endfor								
							</ul>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	@endfor

@endsection