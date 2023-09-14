    #<?= $this->getId(); ?> #<?= $this->getId(); ?>addword {
        width: 10em;
        float: left;
    }
    #<?= $this->getId(); ?> .selectwords .addui .wordleControls {
        vertical-align: top;
        float: left;
        padding-left: 0.8em;
    }
    #<?= $this->getId(); ?> .selectwords .addui .wordleControls input[type=button] {
        margin-bottom: 0.8em;
    }
    
    #<?= $this->getId(); ?> .wordleletter,
    #<?= $this->getId(); ?> .wordlecloseletter {
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
    
    #<?= $this->getId(); ?> .wordlecloseletter {
        color: red;
        background-color: black;
        margin-left: 0.8em;
        font-size: 80%;
        cursor: pointer;
    }
    
    #<?= $this->getId(); ?> .wordleletter.missing {
        background-color: black;
    }
    
    #<?= $this->getId(); ?> .wordleletter.has {
        background-color: #808000;
    }
    
    #<?= $this->getId(); ?> .wordleletter.found {
        background-color: #008000;
    }
