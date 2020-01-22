@if (!empty($bags))
    <script>
        @foreach($bags as $bag)
        $('body').toast({!! json_encode($bag) !!});
        @endforeach
    </script>
@endif
