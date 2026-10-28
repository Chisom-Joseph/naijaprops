@extends('layouts.app')

@section('title', 'Edit Property: ' . $property->title . ' – NaijaProps')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <nav class="flex items-center space-x-2 text-xs font-medium text-slate-500 mb-2">
                <a href="{{ route('properties.index') }}" class="hover:text-blue-800">Properties</a>
                <span>/</span>
                <a href="{{ route('properties.show', $property) }}" class="hover:text-blue-800 truncate max-w-[180px]">{{ $property->title }}</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Edit</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Edit Property Listing</h1>
            <p class="text-sm text-slate-600 mt-1">
                Update listing details, availability status, or manage uploaded photos for <strong>#NP-{{ str_pad((string) $property->id, 4, '0', STR_PAD_LEFT) }}</strong>.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('properties.show', $property) }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900 bg-white border border-slate-200 px-3.5 py-2 rounded-xl shadow-sm transition">
                &larr; View Property
            </a>
        </div>
    </div>

    <!-- Edit Form Card -->
    <form
        method="POST"
        action="{{ route('properties.update', $property) }}"
        enctype="multipart/form-data"
        data-loading-overlay="true"
        data-loading-title="Saving Property Changes..."
        data-loading-subtitle="Updating listing details, availability status, and gallery photos..."
        x-data="{
            submitting: false,
            propertyType: '{{ old('property_type', $property->property_type) }}',
            transactionType: '{{ old('transaction_type', $property->transaction_type) }}',
            price: '{{ old('price', $property->price) }}',
            location: '{{ old('location', $property->location) }}',
            markedForRemoval: [],
            previews: [],
            toggleRemove(id) {
                if (this.markedForRemoval.includes(id)) {
                    this.markedForRemoval = this.markedForRemoval.filter(item => item !== id);
                } else {
                    this.markedForRemoval.push(id);
                }
            },
            handleFiles(event) {
                this.previews = [];
                const files = Array.from(event.target.files || []).slice(0, 10);
                files.forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.previews.push({
                                name: file.name,
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
        @method('PUT')

        <div class="p-6 sm:p-8 space-y-8">
            @if(auth()->user()->isAdmin() && auth()->id() !== $property->owner_id)
                <div class="bg-purple-50 border border-purple-200 text-purple-900 px-4 py-3 rounded-xl text-xs font-medium flex items-center justify-between">
                    <span>
                        <strong>Administrator Mode:</strong> You are editing a property owned by <strong>{{ $property->owner?->full_name }}</strong> ({{ $property->owner?->email }}).
                    </span>
                    <a href="{{ route('admin.properties.index') }}" class="underline font-bold">Admin Properties</a>
                </div>
            @endif

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
                            value="{{ old('title', $property->title) }}"
                            required
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
                                <option value="{{ $key }}" @selected(old('property_type', $property->property_type) === $key)>
                                    {{ $label }}
                                </option>
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
                            <option value="sale" @selected(old('transaction_type', $property->transaction_type) === 'sale')>For Sale (Outright Purchase)</option>
                            <option value="rent" @selected(old('transaction_type', $property->transaction_type) === 'rent')>For Rent (Annual Lease)</option>
                        </select>
                        @error('transaction_type')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Pricing, Status, Location & Specs -->
            <div>
                <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">
                    2. Pricing (₦), Status &amp; Location
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-4">
                    <!-- Price -->
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
                                class="w-full rounded-xl border @error('price') border-red-400 @else border-slate-300 @enderror pl-8 pr-4 py-2.5 text-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700/15 focus:outline-none"
                            >
                        </div>
                        @error('price')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Listing Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="status"
                            name="status"
                            required
                            class="w-full rounded-xl border @error('status') border-red-400 @else border-slate-300 @enderror px-3.5 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                        >
                            @foreach(\App\Models\Property::STATUSES as $statusKey => $statusLabel)
                                <option value="{{ $statusKey }}" @selected(old('status', $property->status) === $statusKey)>
                                    {{ $statusLabel }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
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
                            class="w-full rounded-xl border @error('location') border-red-400 @else border-slate-300 @enderror px-4 py-2.5 text-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700/15 focus:outline-none"
                        >
                        @error('location')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bedrooms & Bathrooms -->
                    <div x-show="propertyType !== 'land'">
                        <label for="bedrooms" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Bedrooms
                        </label>
                        <input
                            type="number"
                            min="0"
                            max="50"
                            id="bedrooms"
                            name="bedrooms"
                            value="{{ old('bedrooms', $property->bedrooms) }}"
                            class="w-full rounded-xl border @error('bedrooms') border-red-400 @else border-slate-300 @enderror px-4 py-2.5 text-sm focus:border-blue-700 focus:outline-none"
                        >
                    </div>

                    <div x-show="propertyType !== 'land'">
                        <label for="bathrooms" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Bathrooms
                        </label>
                        <input
                            type="number"
                            min="0"
                            max="50"
                            id="bathrooms"
                            name="bathrooms"
                            value="{{ old('bathrooms', $property->bathrooms) }}"
                            class="w-full rounded-xl border @error('bathrooms') border-red-400 @else border-slate-300 @enderror px-4 py-2.5 text-sm focus:border-blue-700 focus:outline-none"
                        >
                    </div>
                </div>
            </div>

            <!-- Section 3: Description -->
            <div>
                <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">
                    3. Property Description
                </h2>
                <div class="mt-4">
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Full Description <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        required
                        class="w-full rounded-xl border @error('description') border-red-400 @else border-slate-300 @enderror px-4 py-3 text-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700/15 focus:outline-none"
                    >{{ old('description', $property->description) }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section 4: Existing Images & Upload Additional Images -->
            <div>
                <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">
                    4. Property Photos ({{ $property->images->count() }} Current)
                </h2>

                @if($property->images->isNotEmpty())
                    <div class="mt-4">
                        <p class="text-xs text-slate-500 mb-3">
                            Click <strong>"Remove Photo"</strong> on any existing photo you want deleted when you save changes.
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach($property->images as $img)
                                <div
                                    :class="markedForRemoval.includes({{ $img->id }}) ? 'ring-2 ring-red-500 opacity-60' : 'border-slate-200'"
                                    class="relative rounded-xl border overflow-hidden bg-slate-100 group transition"
                                >
                                    <img src="{{ $img->url }}" alt="Property photo" class="w-full h-28 object-cover">

                                    <input
                                        type="checkbox"
                                        name="remove_images[]"
                                        value="{{ $img->id }}"
                                        class="hidden"
                                        :checked="markedForRemoval.includes({{ $img->id }})"
                                    >

                                    <div class="p-2 bg-white flex items-center justify-between">
                                        <span class="text-[11px] font-medium text-slate-500">
                                            {{ $loop->first ? 'Primary' : 'Photo #' . $loop->iteration }}
                                        </span>
                                        <button
                                            type="button"
                                            @click="toggleRemove({{ $img->id }})"
                                            :class="markedForRemoval.includes({{ $img->id }}) ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100'"
                                            class="text-[11px] font-semibold px-2 py-0.5 rounded-md transition"
                                        >
                                            <span x-text="markedForRemoval.includes({{ $img->id }}) ? 'Marked to Remove' : 'Remove Photo'"></span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Upload More Photos -->
                <div class="mt-5">
                    <label for="images" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Add More Property Images (JPEG, PNG, JPG, WEBP &bull; Max 2MB each)
                    </label>
                    <label
                        for="images"
                        class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-slate-300 hover:border-blue-600 rounded-2xl p-5 bg-slate-50/70 hover:bg-blue-50/30 cursor-pointer transition text-center"
                    >
                        <p class="text-sm font-semibold text-slate-800">Upload additional photos</p>
                        <p class="text-xs text-slate-500 mt-0.5">New files will be added to this property's gallery</p>
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

                    <!-- New Upload Previews -->
                    <template x-if="previews.length > 0">
                        <div class="mt-4">
                            <p class="text-xs font-bold text-emerald-700 mb-2">
                                New Photos to Upload (<span x-text="previews.length"></span>):
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <template x-for="(item, index) in previews" :key="index">
                                    <div class="rounded-xl border border-emerald-200 overflow-hidden bg-emerald-50/30">
                                        <img :src="item.url" :alt="item.name" class="w-full h-24 object-cover">
                                        <div class="p-1.5 bg-white text-[11px] truncate text-slate-700" x-text="item.name"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Form Actions Footer -->
        <div class="px-6 sm:px-8 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3">
            <a href="{{ route('properties.show', $property) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 px-4 py-2.5 rounded-xl">
                Cancel
            </a>
            <button
                type="submit"
                @click="if ($el.form.checkValidity()) submitting = true"
                :disabled="submitting"
                data-loading-text="Saving changes..."
                class="bg-blue-800 hover:bg-blue-700 disabled:opacity-80 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition inline-flex items-center justify-center gap-2"
            >
                <svg x-show="submitting" x-cloak class="animate-spin h-4 w-4 text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="submitting ? 'Saving changes...' : 'Save Property Changes'">Save Property Changes</span>
            </button>
        </div>
    </form>
</div>
@endsection
