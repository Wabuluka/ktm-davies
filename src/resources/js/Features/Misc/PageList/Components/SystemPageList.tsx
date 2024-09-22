import { FC } from 'react';
import { Pages } from '../Types';
import { PageList } from './PageList';

export const SystemPageList: FC = () => {
  const pages: Pages = [
    {
      name: 'Users Setting',
      href: route('users.index'),
      isActive: route().current('users.*'),
    },
    {
      name: 'Bookstores Setting (Physical)',
      href: route('book-stores.index'),
      isActive: route().current('book-stores.*'),
    },
    {
      name: 'Bookstores Setting (eBooks)',
      href: route('ebook-stores.index'),
      isActive: route().current('ebook-stores.*'),
    },
    {
      name: 'Goods-stores Setting',
      href: route('goods-stores.index'),
      isActive: route().current('goods-stores.*'),
    },
  ];

  return <PageList pages={pages} />;
};
