import {Controller} from 'stimulus';
import {useClickOutside, useDebounce} from 'stimulus-use';

/**
 *
 */
export default class extends Controller {

    static targets = ['result']
    static debounces = ['search']
    static values = {
        url: String,
        categoryId: Number
    }

    connect() {
        useClickOutside(this);
        useDebounce(this);
    }

    onSearchInput(event) {
        this.search(event.currentTarget.value);
    }

    selectX(event) {
        const categoryIdSelected = event.currentTarget.dataset.categoryId;
        console.log('id select: ' + categoryIdSelected);
    }

    async search(query) {
        const params = new URLSearchParams({
            q: query,
        });
        console.log("query: " + query);
        const response = await fetch(`${this.urlValue}?${params.toString()}`);
        this.resultTarget.innerHTML = await response.text();
    }

    clickOutside(event) {
        console.log('out');
    //    event.preventDefault();
        this.resultTarget.innerHTML = '';
    }
}
