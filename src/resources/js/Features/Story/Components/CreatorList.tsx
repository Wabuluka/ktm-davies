import {
  CreatorListItem,
  CreatorListItemProps,
} from '@/Features/Story/Components/CreatorListItem';
import { StoryFormData } from '@/Features/Story/Types';
import { List } from '@chakra-ui/react';
import { ComponentProps } from 'react';

type Props = {
  creators: StoryFormData['creators'];
} & {
  onCreatorRemove: CreatorListItemProps['onRemove'];
  onCreatorOrderDown: CreatorListItemProps['onOrderDown'];
  onCreatorOrderUp: CreatorListItemProps['onOrderUp'];
} & Omit<ComponentProps<typeof List>, 'children'>;

export function CreatorList({
  creators,
  onCreatorRemove: onRemove,
  onCreatorOrderDown: onOrderDown,
  onCreatorOrderUp: onOrderUp,
  ...props
}: Props) {
  return (
    <List display="flex" flexDir="column" gap={4} p={4} {...props}>
      {creators.map(({ id }, i) => (
        <CreatorListItem
          key={id}
          creatorId={id}
          onRemove={onRemove}
          onOrderUp={i === 0 ? undefined : onOrderUp}
          onOrderDown={i === creators.length - 1 ? undefined : onOrderDown}
        />
      ))}
    </List>
  );
}
