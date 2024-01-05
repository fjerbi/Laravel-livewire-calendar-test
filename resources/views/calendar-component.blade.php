<div class="container mx-auto px-2 md:px-4 flex flex-col items-center">
    <div class="bg-gray-200 p-4 rounded-lg shadow-lg flex justify-center items-center w-110 mb-4">
        <div class="text-lg text-gray-600 mx-4">
            @if($currentDate->isToday())
            Today
            @else
            {{ $currentDate->format('d. M y') }} - {{ $currentDate->copy()->addDays(6)->format('d. M y') }}
            @endif
        </div>
        <p class="bg-custom-weekend-color rounded-md px-4 py-1 mx-4 text-white font-bold">Heute</p>

        <button wire:click="goToPreviousWeek" class="px-2 py-1 bg-custom-day-color text-white rounded">
            &lt;
        </button>
        &nbsp;
        <button wire:click="goToNextWeek" class="px-2 py-1 bg-custom-day-color text-white rounded">
            &gt;
        </button>
    </div>

    <div class="mx-auto w-12/11 rounded-md overflow-hidden">
        <div class="grid grid-cols-8">
            <div class="text-center px-1 py-1"></div>

            @foreach($weekDates as $date)
            <div class="text-center py-2 @if($date->dayOfWeek === 0 || $date->dayOfWeek === 6) bg-custom-weekend-color @else bg-custom-day-color @endif text-white"
                style="height: 60px;">
                <button wire:click="selectDate('{{ $date->format('Y-m-d') }}')" class="focus:outline-none h-full">
                    {{ $date->format('D') }}
                </button>
            </div>
            @endforeach

            @php
            $hours = ['07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00'];
            @endphp

            @foreach($hours as $hour)
            <div class="text-center px-2 py-1">{{ $hour }}</div>
            @foreach($weekDates as $date)
            @php
            $sessionTime = $date->copy()->setHour(explode(':', $hour)[0])->setMinute(explode(':', $hour)[1]);
            $sessionTimeString = $sessionTime->format('Y-m-d H:i');
            $isBooked = in_array($sessionTimeString, $weekBookedSessions[$loop->parent->index]);
            @endphp
            <div class="text-center">
                <div class="inline-flex flex-col items-center">
                    <button wire:click="bookSession('{{ $sessionTimeString }}')"
                        class="w-12 h-12 md:w-16 md:h-16 xl:w-16 xl:h-16 text-white {{ $isBooked ? 'bg-gray-500' : 'bg-gray-200' }}"
                        style="margin: 0; padding: 0; line-height: 1;" {{ $isBooked ? 'disabled' : '' }}>
                    </button>
                    @if($isBooked && !empty($sessionNames[$sessionTimeString]))
                    <input wire:model="sessionNames['{{ $sessionTimeString }}']" class="mt-2 px-2 py-1 rounded border"
                        type="text" placeholder="Session Name">
                    <button wire:click="saveSessionName('{{ $sessionTimeString }}')"
                        class="mt-1 px-2 py-1 bg-blue-500 text-white rounded">Save</button>
                    @endif
                </div>
            </div>
            @endforeach
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', () => {
            let weekBookedSessions = @json($weekBookedSessions);
            localStorage.setItem('weekBookedSessions', JSON.stringify(weekBookedSessions));
        });
    });
</script>
@endpush