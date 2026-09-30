
document.querySelectorAll('.skillpick').forEach(function (box) {
    var search = box.querySelector('.skillpick-search');
    var counter = box.querySelector('.skillpick-count');
    var other = box.querySelector('.skillpick-other');
    var empty = box.querySelector('.skillpick-empty');
    var chips = box.querySelectorAll('.chip');

    function update() {
        var names = [];
        chips.forEach(function (c) {
            var input = c.querySelector('input');
            if (input.checked) names.push(input.value);
        });
        other.value.split(',').forEach(function (s) {
            if (s.trim() != '') names.push(s.trim());
        });
        counter.textContent = names.length + ' selected';
       
        var tooMany = names.join(', ').length > 250;
        counter.classList.toggle('too-many', tooMany);
        if (tooMany) counter.textContent += ' - too many, remove a few';
    }

    function filter() {
        var word = search.value.trim().toLowerCase();
        var shown = 0;
        box.querySelectorAll('.skillpick-group').forEach(function (g) {
            var inGroup = 0;
            g.querySelectorAll('.chip').forEach(function (c) {
                var match = c.textContent.toLowerCase().indexOf(word) != -1;
                c.style.display = match ? '' : 'none';
                if (match) inGroup++;
            });
            g.style.display = inGroup ? '' : 'none';
            shown += inGroup;
        });
        empty.style.display = shown ? 'none' : 'block';
    }


    search.addEventListener('keydown', function (e) { if (e.key == 'Enter') e.preventDefault(); });
    search.addEventListener('input', filter);
    other.addEventListener('input', update);
    box.addEventListener('change', update);
    update();
});
