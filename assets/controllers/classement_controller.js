import {Controller} from '@hotwired/stimulus';
import {useDebounce} from 'stimulus-use';

/**
 *
 */
export default class extends Controller {

    static targets = ['categories', 'selectedCategory', 'result1', 'result2', 'result3', 'result4', 'result5', 'classementList', 'btn']
    static values = {
        urlGetCategories: String,
        urlDeleteClassement: String,
        urlPrincipalClassement: String,
        urlGetCategory: String,
        categoryId: Number,
        categoryLevel: Number
    }

    connect() {
        useDebounce(this);
    }

    async searchJf(query) {
        const params = new URLSearchParams({
            parentId: query,
            level: this.categoryLevelValue
        });
        const response = await fetch(`${this.urlGetCategoriesValue}?${params.toString()}`, {
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
        this.categoryLevelValue = event.currentTarget.dataset.categoryLevel;
        this.categoryIdValue = categoryIdSelected === this.categoryIdValue ? null : categoryIdSelected;
        this.searchJf(categoryIdSelected);
        this.getCategory(categoryIdSelected);

       // this.categoriesTargets.forEach((element) => {
            //     console.log(element.dataset.categoryId);
       // });
    }

    async delete(classementId) {
        const params = new URLSearchParams({
            classementId: classementId,
        });
        const response = await fetch(`${this.urlDeleteClassementValue}?${params.toString()}`, {
            method: 'POST'
        });
        this.classementListTarget.innerHTML = await response.text();
    }

    deleteClassement(event) {
        const classementIdSelected = event.currentTarget.dataset.classementId;
        console.log('id select: ' + classementIdSelected);
        this.delete(classementIdSelected)
    }

    async principal(classementId) {
        const params = new URLSearchParams({
            classementId: classementId,
        });
        const response = await fetch(`${this.urlPrincipalClassementValue}?${params.toString()}`, {
            method: 'POST'
        });
        this.classementListTarget.innerHTML = await response.text();
    }

    principalClassement(event) {
        const classementIdSelected = event.currentTarget.dataset.classementId;
        this.principal(classementIdSelected)
    }

    async getCategory(categoryId) {
        const params = new URLSearchParams({
            id: categoryId,
        });
        const response = await fetch(`${this.urlGetCategoryValue}?${params.toString()}`, {
            method: 'POST'
        });
        this.btnTarget.innerHTML = '<i class="far fa-save" aria-hidden="true"></i> Ajouter ' + await response.text();
    }

    categoryIdValueChanged() {
        this.selectedCategoryTarget.value = this.categoryIdValue;
    }
}
