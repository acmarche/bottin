<div>
    @include('livewire.front.shop-detail.seo')

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('livewire.front.shop-detail.back-link')

        <div class="grid gap-8 lg:grid-cols-3">
            {{-- Main content --}}
            <div class="lg:col-span-2 space-y-8">
                @include('livewire.front.shop-detail.header')
                @include('livewire.front.shop-detail.description')
                @include('livewire.front.shop-detail.schedules')
                @include('livewire.front.shop-detail.documents')
                @include('livewire.front.shop-detail.gallery')
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                @include('livewire.front.shop-detail.admin-link')
                @include('livewire.front.shop-detail.contact')
                @include('livewire.front.shop-detail.contact-person')
                @include('livewire.front.shop-detail.categories')
                @include('livewire.front.shop-detail.tags')
                @include('livewire.front.shop-detail.map')
            </div>
        </div>
    </section>
</div>
