/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';
import Convert from "ansi-to-html";

const convert = new Convert();

const output = document.getElementById("output");

const url = JSON.parse(document.getElementById("mercure-url").textContent);
const eventSource = new EventSource(url);
eventSource.onmessage = event => {
    // Will be called every time an update is published by the server
    console.log(event.data)
    const data = JSON.parse(event.data);
    if(data.line !== undefined)
    {
        console.log(data.line)
        const lines = data.line.split(/\r?\n/);
        for (let i = 0; i < lines.length-1; i++) {
            console.log(convert.toHtml(lines[i]))
            let row = document.createElement("div");
            row.innerHTML = convert.toHtml(lines[i])
            output.appendChild(row)
        }
        // lines.forEach(line => console.log(convert.toHtml(line)))

    }


}