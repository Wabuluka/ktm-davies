import { NewsStatus } from '@/Features/News/Types';
import { Badge, BadgeProps } from '@chakra-ui/react';

type Props = Omit<BadgeProps, 'children' | 'colorScheme'> & {
  status: NewsStatus;
};

export function NewsStatusBadge({ status, ...statusBadgeProps }: Props) {
  const props: BadgeProps = statusBadgeProps;

  if (status === 'draft') {
    props.colorScheme = 'gray';
    props.children = 'Draft';
  } else if (status === 'willBePublished') {
    props.colorScheme = 'orange';
    props.children = 'WillBePublished';
  } else if (status === 'published') {
    props.colorScheme = 'green';
    props.children = 'Published';
  }

  return <Badge p={2} textAlign="center" borderRadius={8} {...props} />;
}
