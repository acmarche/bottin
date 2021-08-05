import {Controller} from 'stimulus';
import {useDebounce} from 'stimulus-use';

/**
 *
 */
export default class extends Controller {

    static targets = ['categories', 'select', 'result1', 'result2', 'result3', 'result4', 'result5']
    static debounces = ['search']
    static values = {
        url: String,
        categoryId: Number,
        categoryLevel: Number
    }

    connect() {
        useDebounce(this);
    }

    async search(query) {
        const params = new URLSearchParams({
            parentId: query,
            level: this.categoryLevelValue
        });
        console.log("query" + query);
        console.log('level' + this.categoryLevelValue);
        const response = await fetch(`${this.urlValue}?${params.toString()}`, {
            method: 'POST'
        });
        switch (this.categoryLevelValue) {
            case 1:
                this.result2Target.innerHTML = await response.text();
                this.result3Target.innerHTML = '';
                this.result4Target.innerHTML = '';
                break;
            case 2:
                this.result3Target.innerHTML = await response.text();
                this.result4Target.innerHTML = '';
                break;
            case 3:
                this.result4Target.innerHTML = await response.text();
                break;
            default:
                break;
        }
    }

    selectCategory(event) {
        const categoryIdSelected = event.currentTarget.dataset.categoryId;
        const level = event.currentTarget.dataset.categoryLevel;
        console.log(level);
        this.categoryLevelValue = level;
        this.categoryIdValue = categoryIdSelected === this.categoryIdValue ? null : categoryIdSelected;

        this.search(categoryIdSelected);

        this.categoriesTargets.forEach((element) => {
            //     console.log(element.dataset.categoryId);
        });
    }

    categoryIdValueChanged() {
        console.log('change');
        this.selectTarget.value = this.categoryIdValue;
        console.log(this.selectTarget.value);
    }
}
