<div id="<?= $this->getId(); ?>">
    
    <div id="<?= $this->getId(); ?>ui">
        
    </div>
    
    <div class="selectwords">
        <h4>Add word</h4>
        <div id="<?= $this->getId(); ?>waitaddui">Please wait...</div>
        <div id="<?= $this->getId(); ?>addui" class="addui" style="display: none">
            <select id="<?= $this->getId(); ?>addword" size="7" title="Select a word to add">
            </select>
            <span class="wordleControls">
                <input type="button" onclick="wordlebot['<?= $this->getId(); ?>'].wordleAddWord(this)" value="Add" title="Add a selected word" />
                <br />
                <label title="Sort alphabetically">Sort&nbsp;<input type="checkbox" id="<?= $this->getId(); ?>Sort" onchange="wordlebot['<?= $this->getId(); ?>'].wordleLoadWords(this)" value="1" /></label>
            </span>
        </div>
    </div>
    
</div>
