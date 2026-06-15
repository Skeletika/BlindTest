console.log('script');
    function init() {
        console.log('turbo');
        // ===== Tags =====
        const wrapper = document.querySelector('.tags-wrapper');
        const list = document.getElementById('tags-list');
        const addBtn = document.getElementById('add-tag');

        if (wrapper && list && addBtn) {
            let index = list.children.length;

            addBtn.onclick = function () {
                console.log('test bouton');

                const prototype = wrapper.dataset.prototype;
                const newForm = prototype.replace(/__name__/g, index);

                const div = document.createElement('div');
                div.classList.add('tag-item');
                div.innerHTML = newForm;

                list.appendChild(div);
                index++;
            };

            if (list.children.length === 0) {
                addBtn.click();
            }
        }

        // ===== Type d'indice =====
        const typeSelect = document.getElementById('add_question_form_type');
        const clueInput = document.getElementById('add_question_form_path');

        if (typeSelect && clueInput) {
            function updateInputType() {
                const selectedType = typeSelect.value;

                clueInput.value = '';
                clueInput.disabled = false;

                switch (selectedType) {
                    case 'texte':
                        clueInput.disabled = true;
                    case 'image':
                        clueInput.type = 'file';
                        clueInput.accept = 'image/*';
                        break;

                    case 'audio':
                        clueInput.type = 'file';
                        clueInput.accept = 'audio/*';
                        break;

                    case 'video':
                        clueInput.type = 'file';
                        clueInput.accept = 'video/*';
                        break;
                }
            }

            updateInputType();

            typeSelect.onchange = updateInputType;
        }
    }

    document.addEventListener('turbo:load', init);

