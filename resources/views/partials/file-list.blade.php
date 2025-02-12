@php
    if(empty($files)){
        $files = [];
    }

    if(!is_array($files)){
        $files = [$files];
    }
@endphp

<div>
    @foreach($files as $file)
        <div class="d-flex justify-content-between align-items-center border mt-2 px-2 py-1">
            @php
                $splits = explode('/', $file);
                $fileName = end($splits);
            @endphp

            <div>{{$fileName}}</div>
            <a href="{{asset($file)}}"
            class="btn btn-primary btn-sm"
            target="_blank"
            >Open</a>
        </div>
    @endforeach
</div>
