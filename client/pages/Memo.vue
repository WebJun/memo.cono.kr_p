<template>
    <form id="root" @submit="onSubmit">
        <header>
            <b-button
                type="submit"
                variant="outline-light"
                size="sm"
                @click="toggle"
            >
                <hamburger-icon />
            </b-button>
            <b-button
                type="submit"
                variant="primary"
                size="sm"
                @click="setMode('save')"
                >저장</b-button
            >
            <b-button
                type="submit"
                variant="danger"
                size="sm"
                @click="setMode('delete')"
                >삭제</b-button
            >
            <textarea
                rows="1"
                placeholder="검색"
                v-model="keyword"
                @keydown.enter="getMemo"
            ></textarea>
            <div>{{ selectCnt }}개</div>
            <div>P : {{ page }}</div>
        </header>
        <div class="body_wrap">
            <aside :class="{ active: toggleAside }">
                <div class="fixBox">
                    <ul>
                        <a :href="baseUri"
                            ><li><i class="fas fa-home"></i> 홈</li></a
                        >
                        <li @click="deleteAllCookie">쿠키 삭제</li>
                        <a href="#" @click.prevent="logout"
                            ><li>{{ $t('logout') }}</li></a
                        >
                        <li @click="changeLimitsize">limit_size 변경</li>
                        <li @click="movePrevPage">이전 페이지</li>
                        <li @click="moveNextPage">다음 페이지</li>
                    </ul>
                </div>
            </aside>
            <main>
                <section>
                    <article>
                        <input type="hidden" />
                        <label>
                            <input
                                type="checkbox"
                                v-model="newPost.checked"
                                @change="newChangeCheck"
                            />
                            <div style="visibility: hidden">
                                1900-01-01 00:00:00
                            </div>
                        </label>
                        <textarea
                            rows="10"
                            @keydown="newOnKeydown"
                            v-model="newPost.content"
                        ></textarea>
                    </article>
                    <article v-for="(post, index) in posts" :data-index="index">
                        <input type="hidden" :value="post.seq" />
                        <label>
                            <input
                                type="checkbox"
                                v-model="post.checked"
                                @change="changeCheck"
                            />
                            <div>{{ post.updated_at }}</div>
                        </label>
                        <textarea
                            rows="10"
                            @keydown="onKeydown"
                            v-model="post.content"
                        ></textarea>
                    </article>
                </section>
                <footer>
                    <div>
                        <a href="https://www.cono.kr">cono.kr</a>
                        개인정보처리방침 도움말 제휴 문의/피드백
                    </div>
                    <div>
                        Page rendered in 0.0289 seconds [2.08MB] / 메모 ver 1.0
                        By Jun.
                    </div>
                </footer>
            </main>
        </div>
    </form>
</template>

<script>
import HamburgerIcon from '~/components/HamburgerIcon';

const KEY_TAB = 9;
const KEY_CTRL = 17;
const KEY_ALT = 18;
const KEY_ESC = 27;
const KEY_A = 65;
const KEY_C = 67;

class Model {
    constructor(param) {
        this.$axios = param.axios;
        this.$alertify = param.alertify;
    }
    async getPosts(getData) {
        let res = await this.$axios.get(`/memos`, { params: getData });
        return this.getPostsAfter(res);
    }
    async save(postData, posts) {
        let res = await this.$axios.post('/memos', postData);
        return this.saveAfter(postData, posts, res);
    }
    async delete(postData, posts) {
        let res = await this.$axios.put('/memos', postData);
        return this.deleteAfter(postData, posts, res);
    }
    getPostsAfter(res) {
        return res.data.map((post) => {
            post.created_at = this.getDatetime(post.created_at);
            post.updated_at = this.getDatetime(post.updated_at);
            post.checked = false;
            post.updated_at = post.updated_at
                ? post.updated_at
                : post.created_at;
            return post;
        });
    }
    saveAfter(postData, posts, res) {
        let apple = {};
        if (typeof postData.update !== 'undefined') {
            apple.posts = this.editPostData(postData.update, posts);
        }
        if (typeof postData.insert !== 'undefined') {
            postData.insert.id = res.data;
            this.addPostData(postData.insert, posts);
            this.$alertify.success(`추가 성공`);

            apple.newPost = this.initNewPost();
        }
        return apple;
    }
    editPostData(updatedPostData, posts) {
        let updateCnt = updatedPostData.length;
        if (updateCnt > 0) {
            this.$alertify.success(`수정 ${updateCnt}개 성공`);
        }
        // TODO::보내는 동안 체크를 더 할 수 있음. 개는 보내지 않았으니 언체크하면 안됨
        // 보낸 애들만 언체크해야함.
        posts.map((post) => {
            post.checked = false;
        });
        return posts;
    }
    addPostData(insertedPostData, posts) {
        posts.unshift({
            id: insertedPostData.id,
            content: insertedPostData.content,
            checked: false,
            updated_at: this.getDatetime(),
        });
    }
    initNewPost() {
        return {
            content: '',
            checked: false,
        };
    }
    deleteAfter(postData, posts, res) {
        if (res.status !== 204) {
            this.$alertify.error(`삭제실패`);
            return;
        }
        posts = posts.filter((post) => postData.includes(post.id) === false);
        if (postData.length > 0) {
            this.$alertify.error(`삭제 ${postData.length}개 성공`);
        }
        return posts;
    }
    getDatetime(str) {
        let d = new Date();
        if (typeof str !== 'undefined') {
            d = new Date(str);
        }
        let s =
            String(d.getFullYear()).padStart(4, '0') +
            '-' +
            String(d.getMonth() + 1).padStart(2, '0') +
            '-' +
            String(d.getDate()).padStart(2, '0') +
            ' ' +
            String(d.getHours()).padStart(2, '0') +
            ':' +
            String(d.getMinutes()).padStart(2, '0') +
            ':' +
            String(d.getSeconds()).padStart(2, '0');
        return s;
    }
}

export default {
    name: 'App',
    middleware: 'auth',
    layout: 'simple',
    components: {
        HamburgerIcon,
    },
    data: () => ({
        baseUri: '',
        posts: [],
        newPost: {
            content: '',
            updated_at: '',
            checked: false,
        },
        keyword: '',
        keywordLately: '',
        mode: null,
        toggleAside: 0,
        selectCnt: 0,
        limit_size: 0,
        page: 1,
    }),
    created() {
        this.model = new Model({
            axios: this.$axios,
            alertify: this.$alertify,
        });
        this.eventInit();
        this.limit_size = this.$cookies.get('limit_size')
            ? this.$cookies.get('limit_size')
            : 24 - 1;
        this.toggleAside = this.$cookies.get('toggleAside') === '1' ? 1 : 0;
        this.baseUri = location.origin;
        this.setKeywordByUrl();
        this.getMemo();
    },
    methods: {
        async getMemo(e) {
            if (e !== undefined) {
                e.preventDefault();
            }
            if (this.keywordLately !== this.keyword) {
                this.keywordLately = this.keyword;
                this.page = 1;
            }
            this.setUrlByKeyword();
            let getData = {
                keyword: this.keyword,
                limit_size: this.limit_size,
                limit_offset: (this.page - 1) * this.limit_size,
            };
            this.posts = await this.model.getPosts(getData);
        },
        setKeywordByUrl() {
            let url = new URL(location.href);
            let keyword = url.searchParams.get('keyword') ?? '';
            if (keyword === '') {
                return;
            }
            this.keyword = keyword;
        },
        setUrlByKeyword() {
            let url = new URL(location.href);
            if (this.keyword === '') {
                url.searchParams.delete('keyword');
            } else {
                url.searchParams.set('keyword', this.keyword);
            }
            history.pushState(null, null, url.toString());
        },
        onSubmit(e) {
            e.preventDefault();
            if (this.mode == 'save') {
                this.onSubmitSave();
            } else if (this.mode == 'delete') {
                this.onSubmitDelete();
            }
            this.selectCnt = 0;
        },
        async onSubmitSave() {
            let postData = {};
            if (this.newPost.checked === true) {
                postData.insert = {
                    id: this.newPost.id,
                    content: this.newPost.content,
                };
            }
            let updates = this.posts.filter((post) => post.checked);
            if (updates.length > 0) {
                updates = updates.map((post) => {
                    return {
                        id: post.id,
                        content: post.content,
                    };
                });
                postData.update = updates;
            }
            if (Object.keys(postData).length <= 0) {
                return false;
            }
            let { posts, newPost } = await this.model.save(
                postData,
                this.posts
            );
            if (typeof posts !== 'undefined') {
                this.posts = posts;
            }
            if (typeof newPost !== 'undefined') {
                this.newPost = newPost;
            }
        },
        async onSubmitDelete() {
            let postData = this.posts
                .filter((post) => post.checked)
                .map((post) => post.id);
            if (postData.length <= 0) {
                return false;
            }
            this.posts = await this.model.delete(postData, this.posts);
        },
        newOnKeydown(e) {
            if (this.isExceptKey(e) === true) {
                return;
            // unchecked
            } else if (e.keyCode === KEY_ESC) {
                if (this.newPost.checked == true) {
                    this.newPost.checked = false;
                    this.selectCnt--;
                }
            // checked
            } else {
                if (this.newPost.checked == false) {
                    this.newPost.checked = true;
                    this.selectCnt++;
                }
            }
        },
        // @change는 focus out을 해야 동작하기 때문에 keydown 썼음
        onKeydown(e) {
            if (this.isExceptKey(e) === true) {
                return;
            // unchecked
            } else if (e.keyCode === KEY_ESC) {
                let index = e.target.parentNode.dataset.index;
                if (this.posts[index].checked == true) {
                    this.posts[index].checked = false;
                    this.selectCnt--;
                }
            // checked
            } else {
                let index = e.target.parentNode.dataset.index;
                if (this.posts[index].checked == false) {
                    this.posts[index].checked = true;
                    this.selectCnt++;
                }
            }
        },
        isExceptKey(e) {
            if (e.keyCode === KEY_ALT) {
                return true;
            }
            if (e.keyCode === KEY_TAB) {
                return true;
            }
            if (e.ctrlKey === true && e.keyCode === KEY_A) {
                return true;
            }
            if (e.ctrlKey === true && e.keyCode === KEY_C) {
                return true;
            }
            if (e.keyCode === KEY_CTRL) { // 컨트롤 + 다른애들은 체크
                return true;
            }
            return false;
        },
        setMode(s) {
            this.mode = s;
        },
        toggle() {
            this.toggleAside = this.toggleAside == 1 ? 0 : 1;
            this.$cookies.set('toggleAside', this.toggleAside);
        },
        changeCheck(e) {
            let index = e.target.parentNode.parentNode.dataset.index;
            if (this.posts[index].checked == false) {
                this.selectCnt--;
            } else {
                this.selectCnt++;
            }
        },
        newChangeCheck(e) {
            if (this.newPost.checked == false) {
                this.selectCnt--;
            } else {
                this.selectCnt++;
            }
        },
        deleteAllCookie() {
            this.$cookies.keys().forEach((cookie) => {
                // 인증
                if (cookie === 'token') {
                    return;
                }
                this.$cookies.remove(cookie);
            });
            this.$alertify.error(`모든 쿠키 삭제 성공`);
        },
        changeLimitsize() {
            let temp = 23;
            if (
                (temp = prompt(
                    `몇 개의 메모를 가져오시겠습니까? \n기존 : ${this.limit_size}개`
                ))
            ) {
                if (this.limit_size != temp) {
                    this.limit_size = temp;
                    this.page = 1;
                    this.getMemo();
                    this.$alertify.success(`${this.limit_size} limit_size`);
                    this.$cookies.set('limit_size', temp, '10d');
                }
            }
        },
        movePrevPage() {
            this.page -= 1;
            if (this.page < 1) {
                this.page = 1;
                this.$alertify.error(`존재하지 않는 페이지`);
                return false;
            }
            this.getMemo();
            this.$alertify.success(`${this.page} 페이지`);
        },
        moveNextPage() {
            this.page += 1;
            this.getMemo();
            this.$alertify.success(`${this.page} 페이지`);
        },
        eventInit() {
            // 단축키 컨트롤s, 컨트롤d
            window.addEventListener('keydown', (event) => {
                if (event.ctrlKey || event.metaKey) {
                    switch (String.fromCharCode(event.which).toLowerCase()) {
                        case 's':
                            this.mode = 'save';
                            this.onSubmit(event);
                            break;
                        case 'd':
                            this.mode = 'delete';
                            this.onSubmit(event);
                            break;
                    }
                }
            });
        },
        async logout() {
            await this.$store.dispatch('auth/logout');
            this.$router.push({ name: 'login' });
        },
    },
};
</script>

<style scoped>
body,
html {
    height: 100%;
}
body,
button {
    font-size: 12px;
}
#root {
    font-family: 'Noto Sans KR';
    height: 100%;
    font-weight: 300;
}
#root textarea {
    font-family: 'Noto Sans KR';
    font-weight: 300;
}
/* #root {font-family: 'D2 coding'; height:100%; font-weight:400;} */
/* #root textarea {font-family: 'D2 coding'; font-weight:400;}  */
#root header {
    display: flex;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 40px;
    align-items: center;
    background-color: #23282d;
    padding-left: 10px;
    color: #fff;
    font-size: 12px;
}
#root header > * {
    margin-right: 3px;
}
#root header textarea {
    padding: 5px 0 0 5px;
    height: 28px;
    width: 150px;
    border-radius: 5px;
    border: 0px;
    resize: horizontal;
    font-size: 12px;
}
#root .body_wrap {
    display: flex;
    min-height: 100%;
    padding-top: 40px;
}
#root .body_wrap aside {
    width: 160px;
    background-color: #23282d;
}
#root .body_wrap aside .fixBox {
    position: fixed;
    left: 0;
    top: 40px;
    width: 160px;
}
#root .body_wrap aside.active {
    display: none;
}
#root .body_wrap aside ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}
#root .body_wrap aside ul a {
    color: #333;
    text-decoration: none;
}
#root .body_wrap aside ul li {
    padding: 11px;
    cursor: pointer;
    color: #fff;
    font-size: 12px;
}
#root .body_wrap aside ul li:hover {
    background-color: rgb(25, 30, 35);
    color: #00b9eb;
}
#root .body_wrap main {
    display: flex;
    flex-direction: column;
    flex: 1;
    background: linear-gradient(336deg, #add3dc, #a3e3c0);
}
#root .body_wrap main section {
    display: grid;
    grid-gap: 10px;
    padding: 10px 25px 10px 10px;
    grid-template-columns: repeat(6, 1fr);
}
#root .body_wrap main section article label {
    margin: 0;
    width: 100%;
}
#root .body_wrap main section article label input {
    width: 16px;
    height: 16px;
    vertical-align: 0px;
}
#root .body_wrap main section article label div {
    display: inline-block;
    font-size: 12px;
    vertical-align: 3px;
}
#root .body_wrap main section article textarea {
    width: 100%;
    padding-right: 25px;
    border: 0;
    resize: both;
    font-size: 14px;
}
#root .body_wrap main footer {
    margin-top: auto;
    padding: 20px;
    text-align: center;
    font-size: 12px;
}
#root .body_wrap main footer a {
    color: #333;
    text-decoration: none;
}

@media (max-width: 1200px) {
    #root .body_wrap aside {
        display: none;
    }
    #root .body_wrap aside.active {
        display: inline-block;
    }
    #root .body_wrap main section {
        grid-template-columns: repeat(4, 1fr);
    }
}
@media (max-width: 992px) {
    #root .body_wrap main section {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 768px) {
    #root .body_wrap main section {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 576px) {
    #root .body_wrap main section {
        grid-template-columns: repeat(1, 1fr);
        padding: 10px 10px 10px 10px;
    }
}
</style>
