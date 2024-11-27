<div class="d-flex mb-3 gap-50">
    <div class="upload-img-container">
        <input type="hidden" class="img-url-pass img-pass" name="img_pass" name="img_pass">
        <input type="file" accept="image/*" class="hide img-file">
        <div class="inner-container">
            <img src="{{ asset($img) }}" class="img-preview {{ !$img? "hide" : "" }}"></img>
            <div class="inner-inner-container {{ $img? "hide-imp" : "" }}">
                <div class="upload-info">Перетягніть сюди зображення <br> або <br> вставте URL-адресу вище <br> і тицьніть кнопочку</div>
            </div>
        </div>
    </div>
    <div>
        <div class="mb-3">                
            <div class="subtitle3 mb-2">Підтримувані типи файлів:</div>
            <div class="d-flex gap-16">
                <div class="tag-div">JPEG</div>
                <div class="tag-div">PNG</div>
                <div class="tag-div">GIF</div>
                <div class="tag-div">BMP</div>
                <div class="tag-div">WEBP</div>
            </div>
        </div>
        @if($size)
            <div class="mb-3">      
                <div class="subtitle3 mb-2">Рекомендоване розширення зображення:</div>
                <div class="tag-div">{{ $size }}</div>
            </div>
        @endif
        <div class="mb-3">      
            <div class="subtitle3 mb-2">Максимальний розмір зображення:</div>
            <div class="tag-div">512 x 512 пікс.</div>
        </div>
    </div>
</div>