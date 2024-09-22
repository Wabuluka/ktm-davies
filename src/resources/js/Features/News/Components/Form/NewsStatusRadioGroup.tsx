import { NewsStatusBadge } from '@/Features/News/Components/NewsStatusBadge';
import { NewsStatus } from '@/Features/News/Types';
import { HStack, Radio, RadioGroup } from '@chakra-ui/react';
import { ComponentProps } from 'react';

type Props = {
  onChange: (status: NewsStatus) => void;
} & Omit<ComponentProps<typeof RadioGroup>, 'children' | 'onChange'>;

export function NewsStatusRadioGroup({ onChange, ...props }: Props) {
  return (
    <RadioGroup onChange={(value) => onChange(value as NewsStatus)} {...props}>
      <HStack spacing={8} mt={4}>
        <Radio variant="highlight" value="draft">
          <NewsStatusBadge fontSize={12} ml={2} status="draft" />
        </Radio>
        <Radio variant="highlight" value="willBePublished">
          <NewsStatusBadge fontSize={12} ml={2} status="willBePublished" />
        </Radio>
        <Radio variant="highlight" value="published">
          <NewsStatusBadge fontSize={12} ml={2} status="published" />
        </Radio>
      </HStack>
    </RadioGroup>
  );
}
