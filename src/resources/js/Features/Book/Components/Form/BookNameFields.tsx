import { ResponsiveGrid } from '@/UI/Components/Layout/ResponsiveGrid';
import {
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { FC, useCallback } from 'react';
import { useBookForm } from '../../Hooks/useBookForm';

type Field = 'title' | 'title_kana' | 'volume';

type Props = {
  data: Pick<ReturnType<typeof useBookForm>['data'], Field>;
  errors: Pick<ReturnType<typeof useBookForm>['errors'], Field>;
  setData: ReturnType<typeof useBookForm>['setData'];
};

export const BookNameFields: FC<Props> = ({ data, errors, setData }) => {
  const handleChange = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      setData(e.target.name as Field, e.target.value);
    },
    [setData],
  );

  return (
    <VStack>
      <FormControl isInvalid={!!errors.title} isRequired mb={4}>
        <FormLabel>Title</FormLabel>
        <Input
          type="text"
          name="title"
          value={data.title}
          onChange={handleChange}
          fontSize="2xl"
          fontWeight="bold"
          px={6}
          py={8}
          placeholder="Bob's Bizarre Adventure"
        />
        <FormErrorMessage>{errors.title}</FormErrorMessage>
      </FormControl>

      <ResponsiveGrid w="100%">
        <FormControl isInvalid={!!errors.title_kana}>
          <FormLabel>Title Kana</FormLabel>
          <Input
            type="text"
            name="title_kana"
            value={data.title_kana}
            onChange={handleChange}
            placeholder="Bob's Bizarre Adventure"
          />
          <FormErrorMessage>{errors.title_kana}</FormErrorMessage>
        </FormControl>

        <FormControl isInvalid={!!errors.volume}>
          <FormLabel>Volume</FormLabel>
          <Input
            type="text"
            name="volume"
            value={data.volume}
            onChange={handleChange}
            placeholder="42"
          />
          <FormErrorMessage>{errors.volume}</FormErrorMessage>
        </FormControl>
      </ResponsiveGrid>
    </VStack>
  );
};
