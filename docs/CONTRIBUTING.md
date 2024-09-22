# Contribution Guide

## Pull Request

### オープンする粒度

- 基本 1 機能につき 1 Pull Request
- 作業に 1 日以上掛かるようであれば、適宜分割してオープンする

### オープン前の確認事項

1. リント・フォーマットの実行

   KTCMS の場合:

   ```sh
   ./vendor/bin/sail pint
   ./vendor/bin/sail npm run lint
   ./vendor/bin/sail npm run format
   ```

   レーベルサイトの場合:

   ```sh
   npm run lint
   npm run format
   ```

2. テストの実行

   KTCMS の場合:

   ```sh
   ./vendor/bin/sail test
   ```

   レーベルサイトはテスト未作成 (作成予定)

## コーディング規約

### ディレクトリ構成

#### KTCMS

1. resources/js/

   1. Features/

      モデルや機能単位でディレクトリを切り、コンポーネント・型・Hooks 等を設置する

      ```md
      - resources/js/Features/
        - Auth/
          - Components/
          - Hooks/
          - Types/
          - index.ts
        - Book/
          - Components/
          - Hooks/
          - Types/
          - index.ts
        - ...
      ```

      `index.ts` でディレクトリの外から import 可能なモジュールを定義する

      ```tsx
      // index.ts
      export * from './Components/LoginAlert'
      export * from './Components/LoginForm'
      export * from './Types'

      // eslintrc.js
      'no-restricted-imports': [
        'error',
        {
          // ESLint ルールで、Features の外からの import を禁止する
          patterns: ['@/Features/*/*']
        },
      ],
      ```

   1. Layouts/

      Pages/ のコンポーネントから参照されるレイアウト用ファイルを設置する

      ```md
      - Layouts/
        - Authenticated/
        - Guest/
        - ...
      ```

   1. Pages/

      Inertia の `render` メソッドから参照されるコンポーネントを設置する

      ```md
      - Pages/
        - Auth/
        - Books/
        - ...
      ```

   1. UI/

      モデルや機能に依存しないコンポーネントを設置する

      (Components/ 配下は [Chakra UI のコンポーネントの分類](https://chakra-ui.com/docs/components) を参考にする)

      ```md
      - UI/
        - Components/
          - DataDisplay/
          - Form/
          - Layout/
          - ...
        - Hooks/
        - Types/
        - ...
      ```

1. tests/Unit/

   app/ ディレクトリの構造を踏襲し、ファイル名末尾に `Test.php` を付ける。\
   ただし、Controler アクションのテストは別途 Feature ディレクトリに設置する

   ```md
   - tests/Unit/
     - Middleware/
     - Models/
       - BookTest.php
       - GenreTest.php
       - ...
     - Requests/
     - Traits/
   ```

1. tests/Feature/

   Controler アクションのテストを `<機能>/[機能]<アクション>Test.php` として設置する。

   シングルアクションコントローラのような、一位性の高い名前を持つ Controller では、\
   テストファイル名から `[機能]` を省略しても OK

   ```md
   - tests/Feature/
     - Auth/
       - EmailVarificationTest.php
       - ...
     - Book/
       - BookIndexTest.php
       - BookStoreTest.php
       - ...
     - Genre/
       - GenreIndexTest.php
       - GenreStoreTest.php
       - ...
   ```

#### レーベルサイト

1. app/

   下記に該当するファイルを設置する

   - Next.js の規約に関するファイル (page.tsx, layout.tsx 等)
   - 特定のページ・レイアウトからのみ参照されるファイル群\
     (例: `/book/[id]` セグメントにおける `EbookStoreBlock.tsx` 等)
     - ファイル名先頭は大文字にする

   ```md
   - app/
     - book/
       - [id]/
         - BookStoreBlock.tsx
         - EbookStoreBlock.tsx
         - ...
         - page.tsx
     - page.tsx
   - page.tsx
   - layout.tsx
   - Header.tsx
   - Footer.tsx
   ```

1. components/

   下記に該当するファイルを設置する

   - 複数のページ・レイアウトから参照されるコンポーネント

   ```md
   - components/
     - Button.tsx
     - Heading.tsx
   ```

### Laravel

#### テストコードの書き方

1. テストメソッド

   テストメソッドには `@test` アノテーションをつける

   ```php
   // NG:
   public function it_xxxxxx(): void {}
   public function test_xxxxxx(): void {}

   // OK:
   /** @test */
   public function xxxxxx(): void {}
   ```

   - 理由: 長くなりがちなテストメソッド名を少しでも短くするため

   また、メソッドは日本語で書く

   ```php
   /**
    * 認証情報が正しければログインできること
   */
   public function it_can_login_with_correct_credentials(): void {}

   // OK:
   /** @test */
   public function 認証情報が正しければログインできること(): void {}
   ```

   - 理由: メソッド名を考える時間を短縮するため

### React

#### コンポーネントの書き方

1. props

   `Props` という名前で props の型エイリアスを定義する

   ```tsx
   type Props = {
     className?: string
     children: React.ReactNode
   }
   ```

   - 理由: props 名を考える時間を短縮するため

1. 関数コンポーネントの定義

   `export function({}: Props) {}` の形式に統一する。\
   アロー関数や `React.FC` は使用しない

   ```tsx
   export function Button({ className, children }: Props) {
     return <button className={className}>{children}</button>
   }
   ```

   - 理由:
     - 名前付き export について:
       - 特にフレームワークの規約等で default export を強制されない限りは\
         名前付き export が主流であるため
     - アロー関数について:
       - React や Next の公式ドキュメント内のサンプルコードでは、\
         一貫してコンポーネントの定義に、アロー関数ではなく `function` が使われている
       - `missing display name` 等のエラーの原因になり得る
     - `React.FC` について
       - ジェネクスが使えない等のデメリットがあるため
       - 使わなくても特にデメリットがないため

#### イベントハンドラの書き方

1. props

   イベントハンドラを props で受け取る場合は、\
   `on[対象]イベント` のように、`on` 接頭辞に続けて対象の名前を付けたものを props 名として使用する\
   (対象がない場合は、省略して `onSubmit` のような名前にする)

   ```tsx
   type Props = {
     onFormChange: () => void
     onSearchSubmit: (formData: { keyword: string }) => void
   }

   export function SearchForm({ onFormChange, onSearchSubmit }: Props) {}
   ```

   - 理由: props 名を考える時間を短縮するため

1. コールバック

   また、コンポーネント内でイベントハンドラを定義する場合は、\
   `handle[対象]イベント名` のように、`handle` 接頭辞に続けて対象の名前を付けたものを props 名として使用する\
   (対象がない場合は、省略して `handleSubmit` のような名前にする)

   ```tsx
   export function SearchForm({ onFormChange, onSearchSubmit }: Props) {
     const handleFormChange = (e: React.ChangeEvent<HTMLInputElement>) => {
       onFormChange(e)
     }

     const handleSearchSubmit = (e: React.FormEvent<HTMLFormElement>) => {
       e.preventDefault()
       const formData = { keyword: e.currentTarget.keyword.value }
       onSearchSubmit(formData)
     }

     return (
       <form onSubmit={handleSearchSubmit}>
         <input type="text" name="keyword" onChange={handleFormChange} />
         <button type="submit">検索</button>
       </form>
     )
   }
   ```

   - 理由:
     - 関数名を考える時間を短縮するため
     - `on[対象]イベント名` props との名前の重複を防ぐため

### Next.js

#### コンポーネントの書き方

1. page.tsx::

   Next.js の規約に合わせて default export が必要。\
   コンポーネント名はセグメントに合わせて命名する

   ```tsx
   // `/` だけはネーミングのしようがないので `Top` で
   export default function Top() {}
   // `/book`
   export default function Book() {}
   // `/book/[id]`
   export default function BookId() {}
   // `/book/search`
   export default function BookSearch() {}
   ```

   - 理由:
     - コンポーネント名を考える時間を短縮するため
     - 全て `Page` にすることも考えられるが、下記の問題がある
       - 複数の page.tsx を開いた際に見分けが付きにくい
       - 既存の `Page` コンポーネントと名前が被る

   また、metadata の定義はコンポーネントより先に書く

   ```tsx
   // app/book/page.tsx
   export const metadata: Metadata = {}

   export default function Book({ children }: Props) {}

   // app/book/[id]/page.tsx
   type Props = {
     params: { id: string }
   }

   export async function generateMetadata({
     params: { id },
   }: Props): Promise<Metadata> {}

   export default function BookId({ children, params: { id } }: Props) {}
   ```

   - 理由: DOM 上の並びと (meta -> body) に合わせた方が直感的なため

1. layout.tsx:

   こちらも Next.js の規約に合わせて default export が必要。\
   コンポーネント名は役割に合わせて命名する

   ```tsx
   export default function RootLayout() {}
   export default function NewsPageLayout() {}
   ```

   - 理由:
     - シンプルな命名規則に則るより、役割を表す命名を都度考えた方がメリットが大きそうなため
       - 1 サイト辺りの layout.tsx の数は多くて 2, 3 程度だと思われる

#### Server Component

1. `server-only` の利用:

   クライアント側で利用するとエラーになるモジュール (例: `NEXT_PUBLIC_` で始まらない環境変数を参照している) \
   では `server-only` をインポートする

   ```ts
   import { GraphQLClient } from 'graphql-request'
   import 'server-only'

   const client = new GraphQLClient(process.env.GRAPHQL_ENDPOINT || '', {
     headers: {
       authorization: `Bearer ${process.env.GRAPHQL_API_KEY}`,
     },
   })

   export default client
   ```

   - 理由: 無効な import をビルドエラーとして検出できるため\
     (https://beta.nextjs.org/docs/rendering/server-and-client-components#keeping-server-only-code-out-of-client-components-poisoning)
