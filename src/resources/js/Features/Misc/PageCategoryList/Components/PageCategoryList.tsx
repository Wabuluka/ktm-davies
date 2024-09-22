import { List, ListItem, ListItemProps } from '@chakra-ui/react';
import { FC } from 'react';
import { BsBookHalf, BsGear, BsGlobe } from 'react-icons/bs';
import { PageCategory } from '../Types';
import { PageCategoryListItem } from './PageCategoryListItem';

type Props = {
  pageCagetory?: PageCategory;
};

export const PageCategoryList: FC<Props> = ({ pageCagetory }) => {
  const lsitItemProps: ListItemProps = {
    display: { base: 'inline-block', lg: 'block' },
  };

  return (
    <List>
      <ListItem {...lsitItemProps}>
        <PageCategoryListItem
          href={route('books.index')}
          icon={BsBookHalf}
          selected={pageCagetory === 'Book'}
        >
          Book
        </PageCategoryListItem>
      </ListItem>
      <ListItem {...lsitItemProps}>
        <PageCategoryListItem
          href={route('sites.news.index', 1)}
          icon={BsGlobe}
          selected={pageCagetory === 'Site'}
        >
          Site
        </PageCategoryListItem>
      </ListItem>
      <ListItem {...lsitItemProps}>
        <PageCategoryListItem
          href={route('users.index')}
          icon={BsGear}
          selected={pageCagetory === 'System'}
        >
          System
        </PageCategoryListItem>
      </ListItem>
    </List>
  );
};
