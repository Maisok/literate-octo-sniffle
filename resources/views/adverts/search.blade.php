<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все товары</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
@include('components.header-seller')   

<script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script>
<div id="map" class="w-full h-64 mt-28 md:h-96"></div>
<div class="flex justify-center mt-8">
    @include('components.search-form') <!-- Подключение формы поиска -->
</div>
<h3 class="text-2xl font-bold mt-8 mb-4 text-center">Результаты поиска:</h3>

<!-- Фильтры по параметру engine для мобильных устройств -->
<div class="filters bg-white p-3 rounded-lg shadow-md w-full 2xl:hidden">
    <h4 class="text-lg font-semibold mb-3">Фильтры по двигателю:</h4>
    <form method="GET" action="{{ route('adverts.search') }}"> <!-- Укажите правильный маршрут для обработки формы -->
        @foreach($engines as $engine)
            <div class="mb-2">
                <input type="checkbox" name="engines[]" value="{{ $engine }}" id="engine-{{ $engine }}"
                    {{ in_array($engine, request('engines', [])) || !request()->has('engines') ? 'checked' : '' }}
                    class="mr-2 w-5 h-5">
                <label for="engine-{{ $engine }}" class="text-base">{{ !empty($engine) ? ucfirst($engine) : 'Не указан' }}</label>
            </div>
        @endforeach

        <!-- Сохраняем другие параметры запроса -->
        <input type="hidden" name="search_query" value="{{ request('search_query') }}">
        <input type="hidden" name="brand" value="{{ request('brand') }}">
        <input type="hidden" name="model" value="{{ request('model') }}">
        <input type="hidden" name="year" value="{{ request('year') }}">

        <button type="submit" class="bg-blue-500 text-white text-base px-4 py-2 rounded-lg mt-3">Применить фильтры</button>
    </form>
</div>
<!-- Фильтры по параметру engine -->
<div class="filters bg-white p-4 rounded-lg shadow-md w-80 absolute right-0 mt-4 mr-32 hidden 2xl:block">
    <h4 class="text-xl font-semibold mb-4">Фильтры по двигателю:</h4>
    <form method="GET" action="{{ route('adverts.search') }}"> <!-- Укажите правильный маршрут для обработки формы -->
        @foreach($engines as $engine)
            <div>
                <input type="checkbox" name="engines[]" value="{{ $engine }}" id="engine-{{ $engine }}"
                    {{ in_array($engine, request('engines', [])) || !request()->has('engines') ? 'checked' : '' }}
                    class="mr-2">
                <label for="engine-{{ $engine }}" class="text-lg">{{ !empty($engine) ? ucfirst($engine) : 'Не указан' }}</label>
            </div>
        @endforeach

        <!-- Сохраняем другие параметры запроса -->
        <input type="hidden" name="search_query" value="{{ request('search_query') }}">
        <input type="hidden" name="brand" value="{{ request('brand') }}">
        <input type="hidden" name="model" value="{{ request('model') }}">
        <input type="hidden" name="year" value="{{ request('year') }}">

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-4">Применить фильтры</button>
    </form>
</div>

<div class="container mx-auto mt-8 mb-20">
    @if($adverts->count())
        @foreach($adverts as $advert)
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

        <!-- Подключение пагинации -->
        <div class="mt-8">
            @include('components.pagination', ['adverts' => $adverts])
        </div>
    @else
        <p class="text-center text-lg mt-8">Нет результатов для отображения.</p>
    @endif
</div>

<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=9fbfa4df-7869-44a3-ae8e-0ebc49545ea9" type="text/javascript"></script>
<script>
    let mapInitialized = false;
    let myMap;

    ymaps.ready(function() {
        myMap = new ymaps.Map('map', {
            center: [52.753994, 104.622093],
            zoom: 9, 
            controls: []
        });

        // Отключаем взаимодействие с картой
        myMap.behaviors.disable('drag');
        myMap.behaviors.disable('scrollZoom');

        // Массив адресов для геокодирования
        var addresses = @json($addresses);
        var prod_name = @json($prod_name);
        var image_prod = @json($image_prod);
        var advert_ids = @json($advert_ids);

        // URL изображения по умолчанию
        var defaultImageUrl = "{{ asset('images/dontfoto.jpg') }}";

        // Функция для геокодирования и добавления меток на карту
        function geocodeAndAddToMap(address, prod_name, image_url, advert_id) {
            if (address == "Не указан") {
                return; // Пропускаем добавление метки, если адрес отсутствует
            }

            ymaps.geocode(address, {
                results: 1
            }).then(function (res) {
                var firstGeoObject = res.geoObjects.get(0),
                    coords = firstGeoObject.geometry.getCoordinates(),
                    bounds = firstGeoObject.properties.get('boundedBy');

                // Проверяем, существует ли URL изображения
                var imageUrl = image_url ? image_url : defaultImageUrl;

                // Создаем метку с пользовательским контентом
                var placemark = new ymaps.Placemark(coords, {
                    balloonContent: address + '<br><a href="{{ route('adverts.show', '') }}/' + advert_id + '">' + prod_name + '</a><br><img src="' + imageUrl + '" alt="Фото отсутствует" width="100">', // Пользовательский контент в баллуне с изображением и ссылкой
                    hintContent: prod_name // Пользовательский контент в подсказке
                }, {
                    preset: 'islands#darkBlueDotIconWithCaption'
                });

                myMap.geoObjects.add(placemark);

                // Центрируем карту на последней добавленной метке
                myMap.setCenter(coords, 10, {
                    checkZoomRange: true
                });
            });
        }

        // Выполняем геокодирование и добавление меток для каждого адреса
        addresses.forEach(function (address, index) {
            geocodeAndAddToMap(address, prod_name[index], image_prod[index], advert_ids[index]);
        });

        // Обработчик клика на карту
        document.getElementById('map').addEventListener('click', function() {
            if (!mapInitialized) {
                mapInitialized = true;
                // Включаем взаимодействие с картой
                myMap.behaviors.enable('drag');
                myMap.behaviors.enable('scrollZoom');
            }
        });

        // Обработчик ухода курсора с карты
        document.getElementById('map').addEventListener('mouseleave', function() {
            if (mapInitialized) {
                // Отключаем взаимодействие с картой
                myMap.behaviors.disable('drag');
                myMap.behaviors.disable('scrollZoom');
                mapInitialized = false;
            }
        });
    });
</script>
</body>
</html>