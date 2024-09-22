import { NewsStatus } from '@/Features/News/Types';
import { FormHelperText } from '@chakra-ui/react';
import { ComponentProps } from 'react';

type Props = {
  status: NewsStatus;
} & Omit<ComponentProps<typeof FormHelperText>, 'children'>;

function generateHelperText(status: NewsStatus): string {
  switch (status) {
    case 'draft':
      return 'This will not be publised on website';
    case 'willBePublished':
      return 'This will be published on website when the release date arrives';
    case 'published':
      return 'This will be published';
    default:
      throw new Error('Invalid status');
  }
}

export function NewsStatusHelperText({ status, ...props }: Props) {
  const text = generateHelperText(status);
  return <FormHelperText {...props}>{text}</FormHelperText>;
}
