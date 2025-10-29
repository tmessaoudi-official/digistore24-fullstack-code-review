import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';

@Component({
    selector: 'app-root',
    styleUrls: [
        './app.scss'
    ],
    imports: [
        RouterOutlet
    ],
    templateUrl: './app.html'
})
export class App {}
