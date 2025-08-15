<ul class="list-disc list-inside text-sm text-gray-600">
    @foreach ($subClients as $sub)
        <li>{{ $sub->name }}</li>
    @endforeach
</ul>
