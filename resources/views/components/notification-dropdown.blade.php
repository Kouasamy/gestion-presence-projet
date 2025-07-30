@props(['notifications'])

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="icon-btn relative">
        <i class="fa-solid fa-bell" style="font-size: 20px; color: white;"></i>
        @if($notifications->isNotEmpty())
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ $notifications->count() }}
            </span>
        @endif
    </button>

    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-1 z-50">

        @if($notifications->isEmpty())
            <div class="px-4 py-3 text-gray-500 text-sm">
                Aucune notification
            </div>
        @else
            <div class="max-h-96 overflow-y-auto">
                @foreach($notifications as $notification)
                    <div class="px-4 py-3 hover:bg-gray-50 border-b last:border-b-0">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                @if(isset($notification['photo']))
    <img src="{{ $notification['photo'] }}" alt="" class="h-10 w-10 rounded-full">
@else
    <img src="default-photo-url.jpg" alt="" class="h-10 w-10 rounded-full">
@endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                {{ $notification['nom'] ?? 'Nom non disponible' }}
                                </p>
                                <p class="text-sm text-red-600">
                                    Taux de présence critique : {{ $notification['taux'] }}%
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
