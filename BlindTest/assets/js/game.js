import { waapi, animate, stagger } from "animejs";

export function initGame() {
    console.log("Jeu chargé");

    let score = 0;
    const btn = document.getElementById("play");

    if (btn) {
        btn.addEventListener("click", () => {
            const timer = btn.dataset.timer;
            next(timer);
        });
    }
    async function next(timer) {
        const res = await fetch(window.ROUTES.question);
        if (!res.ok) {
            console.error("Erreur HTTP :", res.status);
            return;
        }
        const data = await res.json();

        if (data.finished) {
            finishGame(score);
            return;
        }

        renderQuestion(data, timer);
    }

    function renderReponse(data) {
        const questionContainer = document.getElementById("question-container");
        questionContainer.innerHTML = "";

        const category = document.createElement("span");
        category.classList.add("question-category");
        category.textContent = data.categorie;

        const timerElement = document.createElement("p");
        timerElement.textContent = "🕞0s";

        const response = document.createElement("h2");
        response.classList.add("question-title");
        response.textContent = "La réponse était :";

        const categorieTimer = document.createElement("div");
        categorieTimer.classList.add("categorie-timer");
        categorieTimer.append(category, timerElement);

        const answerContainer = document.createElement("div");
        answerContainer.classList.add("question-clue");

        const answerText = document.createElement("p");
        answerText.textContent = data.answer[0];
        answerContainer.appendChild(answerText);

        const headQuestion = document.createElement("div");
        headQuestion.classList.add("head-question");
        headQuestion.append(categorieTimer, response);

        const inputResponse = document.getElementById("inputResponse");
        if (inputResponse) {
            inputResponse.disabled = true;
        }

        questionContainer.append(headQuestion, answerContainer);
    }
    function renderQuestion(data, timer) {
        const questionContainer = document.getElementById("question-container");
        let seconde;
        seconde = parseFloat(timer / 1000) - 1;

        const answerContainer = document.getElementById("answer-container");
        questionContainer.innerHTML = "";
        answerContainer.innerHTML = "";

        // ----- Question -----

        const category = document.createElement("span");
        category.classList.add("question-category");
        category.textContent = data.categorie;

        const timerElement = document.createElement("p");
        timerElement.textContent = "🕞" + seconde + "s";

        const question = document.createElement("h2");
        question.classList.add("question-title");
        question.textContent = data.question;

        const categorieTimer = document.createElement("div");
        categorieTimer.classList.add("categorie-timer");
        categorieTimer.append(category, timerElement);

        const clueContainer = document.createElement("div");
        clueContainer.classList.add("question-clue");

        const headQuestion = document.createElement("div");
        headQuestion.classList.add("head-question");
        headQuestion.append(categorieTimer, question);

        // Texte
        if (data.indice.text) {
            const clueText = document.createElement("p");
            clueText.textContent = data.indice.text;
            clueContainer.appendChild(clueText);
        }

        // Média
        if (data.indice.path) {
            let media;

            switch (data.indice.type) {
                case "image":
                    media = document.createElement("img");
                    media.src = data.indice.path;
                    break;

                case "video":
                    media = document.createElement("video");
                    media.src = data.indice.path;
                    media.controls = false;
                    media.autoplay = true;
                    break;

                case "audio":
                    media = document.createElement("audio");
                    media.src = data.indice.path;
                    media.autoplay = true;
                    media.controls = false;

                    const visualizer = document.createElement("div");
                    visualizer.classList.add("audio-visualizer");

                    const squares = [];

                    for (let i = 0; i < 5; i++) {
                        const square = document.createElement("div");
                        square.classList.add("square");
                        visualizer.appendChild(square);
                        squares.push(square);
                    }

                    clueContainer.appendChild(visualizer);

                    // IMPORTANT: animation après insertion DOM
                    requestAnimationFrame(() => {
                        waapi.animate(squares, {
                            rotate: "90deg",
                            borderColor: ["#ffb347", "#ff5252"],
                            duration: 500,
                            delay: stagger(100),
                            loop: true,
                        });

                        animate(squares, {
                            scale: [1, 1.5],
                            backgroundColor: ["#ff5252", "#ffb347"],
                            duration: 500,
                            delay: stagger(100),
                            loop: true,
                            alternate: true,
                        });
                    });

                    break;
            }
            if (media) {
                media.classList.add("question-media");
                clueContainer.appendChild(media);
            }
        }

        questionContainer.append(headQuestion, clueContainer);

        // ----- Réponse -----
        const input = document.createElement("input");
        input.id = "inputResponse";
        input.type = "text";
        input.placeholder = "Votre réponse...";
        input.classList.add("answer-input");
        input.autofocus = true;

        input.addEventListener("keydown", (e) => {
            if (e.key !== "Enter") return;

            const value = input.value.trim().toLowerCase();

            const found = data.answer.some(
                (answer) => answer.toLowerCase() === value,
            );

            if (found) {
                const success = document.createElement("div");
                success.classList.add("answer-success");
                success.textContent = "Vous avez trouvé";

                answerContainer.innerHTML = "";
                answerContainer.appendChild(success);
                score++;
            } else {
                input.value = "";
                input.autofocus = true;
            }
        });

        let countdown = setInterval(function () {
            timerElement.textContent = "🕞" + seconde + "s";
            seconde--;
            if (seconde < 0) {
                clearInterval(countdown);
                renderReponse(data);
                setTimeout(() => {
                    next(timer);
                }, "5000");
            }
        }, 1000);

        answerContainer.appendChild(input);
    }
    function finishGame(score) {
        const questionContainer = document.getElementById("question-container");
        questionContainer.innerHTML = "";
        const container = document.createElement("div");
        container.classList.add("align-auto");

        const button = document.createElement("button");
        button.onclick = () => redirectGame(score);
        button.classList.add("btn-primary", "btn");
        button.textContent = "Voir les résultats";

        container.append(button);
        questionContainer.append(container);
    }

    function redirectGame(score) {
        let url = window.ROUTES.result.replace("__score__", score);

        fetch(url)
            .then((r) => r.json())
            .then((data) => {
                if (data.redirect) {
                    window.location.href =
                        data.redirect + "?score=" + data.score;
                }
            });
    }
}
