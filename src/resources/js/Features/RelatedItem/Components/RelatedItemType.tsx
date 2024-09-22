import { Badge, BadgeProps } from '@chakra-ui/react';
import { RelatebleType } from '../Types';

type Props = {
  type: RelatebleType;
} & Omit<BadgeProps, 'colorScheme' | 'variant'>;

export function RelatedItemType({ type, ...relateditemTypeProps }: Props) {
  const props: BadgeProps = relateditemTypeProps;

  switch (type) {
    case 'book':
      props.colorScheme = 'blue';
      props.children = 'Internal';
      break;
    case 'externalLink':
      props.colorScheme = 'gray';
      props.children = 'External';
      break;
    default:
      throw new Error(`${type} is invalid type`);
  }

  return <Badge p={2} textAlign="center" borderRadius={8} {...props} />;
}
