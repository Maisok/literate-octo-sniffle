@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все товары</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans flex flex-col items-center">
   
@include('components.header-seller')   
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script> 
<!-- Рекламный баннер -->
<h1 class="hidden md:block"></h1>
<img src="{{ asset('images/banner.png') }}"  alt="Реклама" class="banner w-full mx-auto rounded-2xl hidden md:block mt-28">
<p class="hidden md:block"></p>

<h2 class="text-2xl font-bold mt-8 mb-4 text-center">Поиск запчастей:</h2>
@include('components.search-form') <!-- Подключение формы поиска -->

<div class="container mx-auto w-full max-w-screen-2xl">        
    @if($adverts->isEmpty())
        <p class="text-center text-lg mt-8">Нет доступных объявлений.</p>
    @else
        @php
            // Фильтруем коллекцию, исключая товар с id 1111
            $filteredAdverts = $adverts->reject(function($advert) {
                return $advert->id == 1111;
            });
        @endphp

        @foreach($filteredAdverts as $advert)
        <div class="bg-white rounded-lg shadow-md flex max-w-5xl p-4 mt-8 cursor-pointer transition-colors duration-300 hover:bg-blue-100" onclick="location.href='{{ route('adverts.show', $advert->id) }}'" tabindex="0" role="button">
            <!-- Вывод главного фото -->
            @if ($advert->main_photo_url)
                <img src="{{ $advert->main_photo_url }}" alt="{{ $advert->product_name }} - Главное фото" class="rounded-lg w-1/5 object-cover" >
            @else
                <img src="{{ asset('images/dontfoto.jpg') }}" alt="Фото отсутствует" class="rounded-lg w-1/5 object-cover">
            @endif
        
            <div class="flex flex-col justify-between w-full pl-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $advert->product_name }}</h2>
                        <p class="beg bg-gray-200 px-3 py-1 w-24 text-sm rounded-lg text-center">{{ $advert->number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-semibold">{{ $advert->price }} ₽</p>
                        <p class="text-red-500">{{ $advert->user->userAddress->city ?? 'Не указан' }}</p>
                    </div>
                </div>
                <div class="flex space-x-2 mt-4 w-full justify-around">
                    <span class="bg-yellow-200 text-yellow-800 text-sm font-semibold px-2.5 py-0.5 rounded">{{ $advert->brand }}</span>
                    <span class="bg-yellow-200 text-yellow-800 text-sm font-semibold px-2.5 py-0.5 rounded">{{ $advert->model }}</span>
                    <span class="bg-yellow-200 text-yellow-800 text-sm font-semibold px-2.5 py-0.5 rounded">{{ $advert->body }}</span>
                    <span class="bg-yellow-200 text-yellow-800 text-sm font-semibold px-2.5 py-0.5 rounded">{{ $advert->engine }}</span>
                </div>
            </div>
        </div>
@endforeach
    <div class="h-24">
        @include('components.pagination', ['adverts' => $adverts])
    </div>
        <!-- Подключение пагинации -->
        
    @endif
</div>
</body>
</html>
@endsection