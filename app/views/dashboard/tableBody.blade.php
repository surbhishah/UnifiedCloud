
{{-- only for test */ $tb = array( array("name" => "file1.txt" , "mod" => "2014-01-08 06:35:35" , "size" => "3k" , "type" => "text"), array("name" => "file1.txt" , "mod" => "2014-01-08 06:35:35" , "size" => "3k" , "type" => "text"), array("name" => "file1.txt" , "mod" => "2014-01-08 06:35:35" , "size" => "3k" , "type" => "text"), array("name" => "file1.txt" , "mod" => "2014-01-08 06:35:35" , "size" => "3k" , "type" => "text")); /* --}}

    @foreach ( $tb as $row)
        <tr>
        @foreach ( $row as $col)
            <td>{{ $col }}</td>
        @endforeach
       </tr>
    @endforeach
