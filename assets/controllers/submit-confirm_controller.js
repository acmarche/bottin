import {Controller} from '@hotwired/stimulus';
import {useDispatch} from 'stimulus-use';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        title: String,
        text: String,
        icon: String,
        confirmButtonText: String,
        submitAsync: Boolean,
    }

    connect() {
        useDispatch(this);
    }

    onSubmit(event) {
        event.preventDefault();
    }

    async submitForm() {
        if (!this.submitAsyncValue) {
            this.element.submit();

            return;
        }

        const response = await fetch(this.element.action, {
            method: this.element.method,
            body: new URLSearchParams(new FormData(this.element)),
        });

        this.dispatch('async:submitted', {
            response,
        });
    }
}
