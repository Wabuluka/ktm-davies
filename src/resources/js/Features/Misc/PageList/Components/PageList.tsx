import { Tree } from '@/UI/Components/DataDisplay/Tree';
import { TreeItem } from '@/UI/Types';
import { List, ListItem } from '@chakra-ui/react';
import { Page } from '../Types';
import { PageListItem } from './PageListItem';

type Props = {
  pages: TreeItem<Page>[];
};

export const PageList = ({ pages }: Props) => {
  return (
    <List>
      {pages.map((page, i) => (
        <ListItem key={i} sx={{ button: { color: 'white' } }}>
          <Tree<Page> item={page} renderItem={PageListItem} />
        </ListItem>
      ))}
    </List>
  );
};
