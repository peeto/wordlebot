class wordlebotClass {

    constructor (id, routeurl) {
        this.id = id;
        this.routeurl = routeurl;
        window.onload = this.wordleLoadWords();
    }

    wbge (id) {
        return document.getElementById(this.id + id);
    }

    wordleAddWord() {
        let wordleui = this.wbge('ui');
        let values = this.wbge('addword');
        let value = values.options[values.selectedIndex];
        let _self = this;

        if (value && value.innerHTML) {
            let text = value.innerHTML;

            let div = document.createElement('div');
            div.classList.add('wordleword');
            wordleui.appendChild(div);

            for (var i = 0; i < text.length; i++) {
                let span = document.createElement('span');
                div.appendChild(span);
                span.classList.add('wordleletter');
                span.classList.add('missing');
                span.dataset.position = i + 1;
                span.dataset.word = text;
                span.onclick = function(e) {
                    _self.wordleChangeLetterState(_self, e);
                }
                span.innerHTML = text[i];
                span.setAttribute('title', 'Set \'' + text[i] + '\' to be not in the word, in the word but not in correct place, or in the correct place');
            }

            let span = document.createElement('span');
            div.appendChild(span);
            span.classList.add('wordlecloseletter');
            span.onclick = function(e) {
                _self.wordleRemoveWord(_self, e);
            }
            span.innerHTML = '\u2718';
            span.setAttribute('title', 'Delete ' + text);

            this.wordleLoadWords();
        }
    }

    wordleLoadingWords() {
        this.wbge('addui').style.display = 'none';
        this.wbge('waitaddui').style.display = 'block';
    }

    wordleGetData() {
        let data = {};
        data['letters'] = [];

        let letters_list = this.wbge('').querySelectorAll('.wordleletter');
        let letters = [...letters_list];
        letters.forEach((e) => {

            let letterdata = data['letters'][data['letters'].length] = {
                'letter': e.innerHTML,
                'position': e.dataset.position,
                'word': e.dataset.word
            };

            if (e.classList.contains('missing')) {
                letterdata['state'] = 'missing';
            } else if (e.classList.contains('has')) {
                letterdata['state'] = 'has';
            } else if (e.classList.contains('found')) {
                letterdata['state'] = 'found';
            }
        });

        data['sort'] = this.wbge('Sort').checked ? '1' : '';

        return data;
    }

    async wordleLoadWords() {
        this.wordleLoadingWords();
        let inputdata = this.wordleGetData();
        let response = await fetch(this.routeurl, {
            'method': 'POST',
            'body': JSON.stringify(inputdata)
        });
        let data = await response.json();

        let opts = this.wbge('addword');
        let ol = opts.options.length;

        opts.innerHTML = ''; //for (var i = 0; i < ol; i++) opts.remove(i);
        data.forEach(i => {
            var opt = document.createElement('option');
            opt.innerHTML = i.word;
            opts.appendChild(opt);            
        });

        this.wbge('addui').style.display = 'block';
        this.wbge('waitaddui').style.display = 'none';
    }

    wordleChangeLetterState(foobar, ev) {
        let e = ev.target ? ev.target : ev.srcElement;
        if (e.classList.contains('missing')) {
            e.classList.remove('missing');
            e.classList.add('has');
        } else if (e.classList.contains('has')) {
            e.classList.remove('has');
            e.classList.add('found');
        } else if (e.classList.contains('found')) {
            e.classList.remove('found');
            e.classList.add('missing');
        } else {
            e.classList.add('missing');
        }
        foobar.wordleLoadWords();
    }

    wordleRemoveWord(foobar, ev) {
        let e = ev.target ? ev.target : ev.srcElement;
        e.parentElement.remove();
        foobar.wordleLoadWords();
    }
}

var wordlebot = function(id) {
    return this[id];
};
wordlebot.add = function(id, routeurl) {
    this[id] = new wordlebotClass(id, routeurl);
};
