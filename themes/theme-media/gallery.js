window.addEventListener('DOMContentLoaded', (event) => {
    var wireMedia = function (data, thumbs, content) {
        var thumb = document.createElement('img');
        thumb.src = data.thumb;
        thumb.alt = data.alt;
        thumb.classList.add('thumbnail');
        thumbs.append(thumb);
        thumb.onclick = function (e) {
            var ts = thumbs.getElementsByClassName('thumbnail');
            for (let i = 0; i < ts.length; i++) {
                ts[i].classList.remove('active');
            }
            thumb.classList.add('active');
            content.innerHTML = data.html;
        }
    }
    var populateGallery = function (gallery, data) {
        var thumbs = document.createElement('div');
        thumbs.classList.add('gallery-thumbs');
        gallery.append(thumbs);
        var content = gallery.getElementsByClassName('gallery-content')[0];
        content.innerHTML = '';
        // loop through data
        for (let i = 0; i < data.length; i++) {
            wireMedia(data[i], thumbs, content);
        }
        // click first thumb
        thumbs.getElementsByClassName('thumbnail')[0].classList.add('active');
        content.innerHTML = data[0].html
    }
    var buildGallery = function (gallery) {
        gallery.classList.add('active');
        gallery.classList.add('loading');
        // load data
        var request = new XMLHttpRequest();
        request.open('GET', gallery.getAttribute('data-gallery-json'));
        request.onload = function () {
            if (this.status >= 200 && this.status < 400) {
                populateGallery(gallery, JSON.parse(this.response));
            } else {
                gallery.classList.add('error');
                gallery.classList.remove('loading');
            }
        };
        request.onerror = function () {
            gallery.classList.add('error');
            gallery.classList.remove('loading');
        }
        request.send();
    };

    var galleries = document.getElementsByClassName('media-gallery');
    for (let i = 0; i < galleries.length; i++) {
        buildGallery(galleries[i]);
        const gallery = galleries[i];
    }
});