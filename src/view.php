<?php
$routepath = '?route=search';

if (isset($options) 
        && isset($options['routepath'])) {
    $routepath = $options['routepath'];
}

if (isset($options) 
        && isset($options['includejquery']) 
        && $options['includejquery']) {
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<?php
}
?>
<style type="text/css">
    #wordlebot #wordleaddword {
        width: 10em;
    }
    #wordlebot .selectwords .addui input {
        vertical-align: top;
    }
    
    #wordlebot .wordleletter, #wordlebot .wordlecloseletter {
        color: white;
        display: inline-block;
        width: 1.5em;
        height: 1.5em;
        margin: 0.2em;
        border-style: solid;
        border-width: 2px;
        border-color: white;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
    }
    
    #wordlebot .wordlecloseletter {
        color: red;
        background-color: black;
        margin-left: 0.8em;
        font-size: 80%;
        cursor: pointer;
    }
    
    #wordlebot .wordleletter.missing {
        background-color: black;
    }
    
    #wordlebot .wordleletter.has {
        background-color: #808000;
    }
    
    #wordlebot .wordleletter.found {
        background-color: #008000;
    }
    
</style>
<div id="wordlebot">
    
    <div id="wordleui">
        
    </div>
    
    <div class="selectwords">
        <h4>Add word</h4>
        <div id="wordlebotwaitaddui">Please wait...</div>
        <div id="wordlebotaddui" class="addui" style="display: none">
            <select id="wordleaddword" size="7">
            </select>
            &nbsp;<input type="button" onclick="wordleAddWord()" value="Add" />
            &nbsp;<input type="button" onclick="wordleSortWords()" value="Sort" />
        </div>
    </div>
    
</div>
<script language="javascript">

function wordleAddWord() {
    var value = $('#wordleaddword option:selected');
    if (value && value.text()) {
        var text = value.text();
        var div = $('<div>', {
            'class': 'wordleword'
        });
        $('#wordleui').append(div);
        for (var i = 0; i < text.length; i++) {
            $(div).append($('<span>', {
                'class': 'wordleletter missing',
                'data-position': i + 1,
                'onclick': 'worldChangeLetterState(this)',
                'text': text[i],
                'title': 'Set letter to not in word, in the word but not in correct place, or in the correct place'
            }));
        }
        $(div).append($('<span>', {
            'class': 'wordlecloseletter',
            'onclick': 'wordleRemoveWord(this)',
            'text': '\u2718',
            'title': 'Click to delete ' + text
        }));
        
        wordleLoadWords();
    }
}

function wordleLoadingWords() {
    $('#wordlebotaddui').hide();
    $('#wordlebotwaitaddui').show();
}

function wordleGetData() {
    var data = [];
    
    $('#wordleui .wordleletter').each(function() {
        var letter = $(this);
        
        if (letter.hasClass('missing')) {
            data[data.length] = {
                'letter': letter.text(),
                'state': 'missing',
                'position': letter.data('position')
            };
        } else if (letter.hasClass('has')) {
            data[data.length] = {
                'letter': letter.text(),
                'state': 'has',
                'position': letter.data('position')
            };
        } else if (letter.hasClass('found')) {
            data[data.length] = {
                'letter': letter.text(),
                'state': 'found',
                'position': letter.data('position')
            };
        }
    });
    
    return data;
}

var wordleAjax;

function wordleLoadWords() {
    wordleLoadingWords();
    if (wordleAjax && wordleAjax.abort) wordleAjax.abort();
    var data = wordleGetData();
    wordleAjax = $.ajax({
        'url': '<?= $routepath ?>',
        'type' : 'POST',
        'data': JSON.stringify(data),
        'success': function(result){
            $('#wordleaddword').empty();
            var json = JSON.parse(result);
            $.each(json, function (i, item) {
                $('#wordleaddword').append($('<option>', {
                    'text': item.word
                }));
            });
            $('#wordlebotwaitaddui').hide();
            $('#wordlebotaddui').show();
        }
    });
}

function worldChangeLetterState(e) {
    var letter = $(e);
    if (letter.hasClass('missing')) {
        letter.removeClass('missing');
        letter.addClass('has');
    } else if (letter.hasClass('has')) {
        letter.removeClass('has');
        letter.addClass('found');
    } else if (letter.hasClass('found')) {
        letter.removeClass('found');
        letter.addClass('missing');
    } else {
        letter.addClass('missing');
    }
    wordleLoadWords();

}

function wordleSortWords() {
    $('#wordleaddword').html($('#wordleaddword option').sort(function (a, b) {
        return a.text == b.text ? 0 : a.text < b.text ? -1 : 1;
    }));
}

function wordleRemoveWord(e) {
    var word = $(e).parent();
    word.remove();
    wordleLoadWords();
}

$(function() {
    wordleLoadWords();
});

</script>
