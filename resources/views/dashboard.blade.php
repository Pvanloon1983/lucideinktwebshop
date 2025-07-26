<x-dashboard-layout>
<main class="container page dashboard">
    <h1>Dashboard</h1>
    @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    @endif
</main>
</x-dashboard-layout>