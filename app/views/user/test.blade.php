{{-- $message --}}

 <ul>
      @foreach($errors->all() as $error)
         <li>{{ $error }}</li>
      @endforeach
   </ul>

{{ Input::old('first_name') }}
{{ Input::old('last_name') }}
