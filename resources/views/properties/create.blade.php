@extends('layouts.app')

@section('title', 'List a New Property – NaijaProps')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <nav class="flex items-center space-x-2 text-xs font-medium text-slate-500 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-800">Dashboard</a>
                <span>/</span>
                <a href="{{ route('properties.my') }}" class="hover:text-blue-800">My Listings</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Create Listing</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">List a New Property</h1>
            <p class="text-sm text-slate-600 mt-1">
                Publish a verified Nigerian property for sale or rent across Lagos, Abuja, Port Harcourt, or nationwide.
            </p>
        </div>

        <a href="{{ route('properties.my') }}" class="self-start sm:self-auto text-xs font-semibold text-slate-700 hover:text-slate-900 bg-white border border-slate-200 px-3.5 py-2 rounded-xl shadow-sm transition">
            &larr; Back to My Listings
        </a>
    </div>

    <!-- Create Form Card -->
    <form
        method="POST"
        action="{{ route('properties.store') }}"
        enctype="multipart/form-data"
        data-loading-overlay="true"
        data-loading-title="Publishing Property Listing..."
        data-loading-subtitle="Uploading property photos and indexing your listing on NaijaProps..."
        x-data="{
            submitting: false,
            propertyType: '{{ old('property_type', 'duplex') }}',
            transactionType: '{{ old('transactionType', old('transaction_type', 'sale')) }}',
            price: '{{ old('price', '') }}',
            location: '{{ old('location', '') }}',
            previews: [],
            handleFiles(event) {
                this.previews = [];
                const files = Array.from(event.target.files || []).slice(0, 10);
                files.forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.previews.push({
                                name: file.name,
                                size: (file.size / 1024).toFixed(1) + ' KB',
                                url: e.target.result
                            });
                        };
                        reader.readAsDataURL(file);
                    }
                });
            },
            formattedNaira() {
                const num = parseFloat(this.price);
                if (isNaN(num) || num <= 0) return '';
                return '₦' + num.toLocaleString('en-NG');
            }
        }"
        class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
    >
        @csrf

        <div class="p-6 sm:p-8 space-y-8">
            <!-- Section 1: Basic Information -->
            <div>
                <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">
                    1. Property Title &amp; Classification
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-4">
                    <!-- Title -->
                    <div class="sm:col-span-2">
                        <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Property Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            placeholder="e.g. 4 Bedroom Fully Detached Duplex with BQ in Lekki Phase 1"
                            class="w-full rounded-xl border @error('title') border-red-400 @else border-slate-300 @enderror px-4 py-2.5 text-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700/15 focus:outline-none"
                        >
                        @error('title')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Property Type -->
                    <div>
                        <label for="property_type" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Property Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="property_type"
                            name="property_type"
                            x-model="propertyType"
                            required
                            class="w-full rounded-xl border @error('property_type') border-red-400 @else border-slate-300 @enderror px-3.5 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                        >
                            @foreach(\App\Models\Property::PROPERTY_TYPES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('property_type')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction Type -->
                    <div>
                        <label for="transaction_type" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Transaction Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="transaction_type"
                            name="transaction_type"
                            x-model="transactionType"
                            required
                            class="w-full rounded-xl border @error('transaction_type') border-red-400 @else border-slate-300 @enderror px-3.5 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                        >
                            <option value="sale">For Sale (Outright Purchase)</option>
                            <option value="rent">For Rent (Annual Lease)</option>
                        </select>
                        @error('transaction_type')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Pricing, Location & Specifications -->
            <div>
                <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">
                    2. Pricing (₦), Location &amp; Specifications
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-4">
                    <!-- Price in Naira -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="price" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                                Price in Nigerian Naira (₦) <span class="text-red-500">*</span>
                            </label>
                            <span x-show="formattedNaira()" x-text="formattedNaira()" class="text-xs font-extrabold text-blue-800"></span>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 font-bold text-sm">₦</span>
                            <input
                                type="number"
                                step="0.01"
                                min="1000"
                                id="price"
                                name="price"
                                x-model="price"
                                required
                                placeholder="e.g. 85000000"
                                class="w-full rounded-xl border @error('price') border-red-400 @else border-slate-300 @enderror pl-8 pr-4 py-2.5 text-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700/15 focus:outline-none"
                            >
                        </div>
                        @error('price')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Initial Listing Status -->
                    <div>
                        <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Listing Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                        >
                            @foreach(\App\Models\Property::STATUSES as $statusKey => $statusLabel)
                                <option value="{{ $statusKey }}" @selected(old('status', 'available') === $statusKey)>
                                    {{ $statusLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location -->
                    <div class="sm:col-span-2">
                        <label for="location" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Location (Area &amp; City) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="location"
                            name="location"
                            x-model="location"
                            required
                            placeholder="e.g. Lekki Phase 1, Lagos / Maitama, Abuja / Old GRA, Port Harcourt"
                            class="w-full rounded-xl border @error('location') border-red-400 @else border-slate-300 @enderror px-4 py-2.5 text-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700/15 focus:outline-none"
                        >
                        <!-- Quick Nigerian City Pills -->
                        <div class="mt-2 flex flex-wrap items-center gap-1.5">
                            <span class="text-[11px] text-slate-400 mr-1">Quick select:</span>
                            @foreach(['Lekki Phase 1, Lagos', 'Victoria Island, Lagos', 'Ikoyi, Lagos', 'Ikeja GRA, Lagos', 'Maitama, Abuja', 'Guzape, Abuja', 'Wuse 2, Abuja', 'Old GRA, Port Harcourt', 'Peter Odili Road, Port Harcourt', 'Eliozu, Port Harcourt'] as $presetLoc)
                                <button
                                    type="button"
                                    @click="location = '{{ $presetLoc }}'"
                                    class="text-[11px] font-medium bg-slate-100 hover:bg-blue-50 hover:text-blue-800 text-slate-600 px-2.5 py-1 rounded-lg border border-slate-200 transition"
                                >
                                    {{ $presetLoc }}
                                </button>
                            @endforeach
                        </div>
                        @error('location')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bedrooms & Bathrooms (Hidden automatically for Land) -->
                    <div x-show="propertyType !== 'land'" class=" transition">
                        <label for="bedrooms" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Bedrooms
                        </label>
                        <input
                            type="number"
                            min="0"
                            max="50"
                            id="bedrooms"
                            name="bedrooms"
                            value="{{ old('bedrooms', 3) }}"
                            placeholder="e.g. 4"
                            class="w-full rounded-xl border @error('bedrooms') border-red-400 @else border-slate-300 @enderror px-4 py-2.5 text-sm focus:border-blue-700 focus:outline-none"
                        >
                        @error('bedrooms')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="propertyType !== 'land'" class=" transition">
                        <label for="bathrooms" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Bathrooms
                        </label>
                        <input
                            type="number"
                            min="0"
                            max="50"
                            id="bathrooms"
                            name="bathrooms"
                            value="{{ old('bathrooms', 4) }}"
                            placeholder="e.g. 4"
                            class="w-full rounded-xl border @error('bathrooms') border-red-400 @else border-slate-300 @enderror px-4 py-2.5 text-sm focus:border-blue-700 focus:outline-none"
                        >
                        @error('bathrooms')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Detailed Description -->
            <div>
                <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">
                    3. Detailed Property Description
                </h2>
                <div class="mt-4">
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Full Description, Amenities &amp; Title Documents <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        required
                        placeholder="Describe key features such as road network, power supply, estate security, boys quarters (BQ), parking space, and title documentation (Governor's Consent, C of O, Deed of Conveyance)..."
                        class="w-full rounded-xl border @error('description') border-red-400 @else border-slate-300 @enderror px-4 py-3 text-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700/15 focus:outline-none"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section 4: Multiple Image Uploads -->
            <div>
                <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">
                    4. Property Photos (Multiple Upload)
                </h2>
                <div class="mt-4">
                    <label for="images" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Upload Property Images (JPEG, PNG, JPG, WEBP &bull; Max 2MB each &bull; Up to 10 files)
                    </label>

                    <label
                        for="images"
                        class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-slate-300 hover:border-blue-600 rounded-2xl p-6 bg-slate-50/70 hover:bg-blue-50/30 cursor-pointer transition text-center"
                    >
                        <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-800">Click to select multiple property photos</p>
                        <p class="text-xs text-slate-500 mt-0.5">Stored securely in <code class="text-[11px] bg-slate-200/70 px-1.5 py-0.5 rounded">storage/app/public/properties</code></p>
                        <input
                            type="file"
                            id="images"
                            name="images[]"
                            multiple
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                            @change="handleFiles($event)"
                            class="mt-3 text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-800 file:text-white hover:file:bg-blue-700"
                        >
                    </label>

                    <!-- Client-Side Selected Image Previews -->
                    <template x-if="previews.length > 0">
                        <div class="mt-4">
                            <p class="text-xs font-bold text-slate-700 mb-2">
                                Selected Photos (<span x-text="previews.length"></span>):
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <template x-for="(item, index) in previews" :key="index">
                                    <div class="relative rounded-xl border border-slate-200 overflow-hidden bg-slate-100">
                                        <img :src="item.url" :alt="item.name" class="w-full h-28 object-cover">
                                        <div class="p-2 bg-white text-[11px] truncate text-slate-700 font-medium" x-text="item.name"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Form Actions Footer -->
        <div class="px-6 sm:px-8 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3">
            <a href="{{ route('properties.my') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 px-4 py-2.5 rounded-xl">
                Cancel
            </a>
            <button
                type="submit"
                @click="if ($el.form.checkValidity()) submitting = true"
                :disabled="submitting"
                data-loading-text="Publishing listing..."
                class="bg-blue-800 hover:bg-blue-700 disabled:opacity-80 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition inline-flex items-center justify-center gap-2"
            >
                <svg x-show="submitting" x-cloak class="animate-spin h-4 w-4 text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="submitting ? 'Publishing listing...' : 'Publish Property Listing'">Publish Property Listing</span>
            </button>
        </div>
    </form>
</div>
@endsection
