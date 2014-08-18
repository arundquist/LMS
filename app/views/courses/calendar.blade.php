BEGIN:VCALENDAR

X-WR-CALNAME:{{$title}}

@foreach ($dates AS $date)
BEGIN:VEVENT
DTSTART;TZID=America/Chicago:{{$date->date->format('Ymd\THis') }}

DTEND;TZID=America/Chicago:{{$date->date->addHour()->format('Ymd\THis') }}



DESCRIPTION: {{$date->details}}


SUMMARY:{{$date->maintopic}}

END:VEVENT
@endforeach
END:VCALENDAR