@foreach ($course->students AS $student)
{{$student->id}}: {{$student->name}}:
{{$student->totals['totals'][-1]}}<br/>
@endforeach