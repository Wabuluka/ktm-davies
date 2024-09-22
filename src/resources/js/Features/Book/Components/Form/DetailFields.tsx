import { useBookForm } from '@/Features/Book/Hooks/useBookForm';
import RichTextEditor from '@/Features/RichTextEditor';
import {
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
} from '@chakra-ui/react';
import { FC } from 'react';

type Field =
  | 'caption'
  | 'description'
  | 'keywords'
  | 'trial_url'
  | 'survey_url';

type Props = {
  data: Pick<ReturnType<typeof useBookForm>['data'], Field>;
  errors: Pick<ReturnType<typeof useBookForm>['errors'], Field>;
  setData: ReturnType<typeof useBookForm>['setData'];
};

export const DetailFields: FC<Props> = ({ data, errors, setData }) => {
  function onTextChange(e: React.ChangeEvent<HTMLInputElement>) {
    setData(e.target.name as Field, e.target.value);
  }
  function setDesription(value: string) {
    setData('description', value);
  }

  return (
    <>
      <FormControl isInvalid={!!errors.caption}>
        <FormLabel>Caption</FormLabel>
        <Input
          type="text"
          name="caption"
          value={data.caption}
          onChange={onTextChange}
          placeholder="What did I do 10 minutes ago?"
        />
        <FormErrorMessage>{errors.caption}</FormErrorMessage>
      </FormControl>

      <FormControl isInvalid={!!errors.description}>
        <FormLabel>Description</FormLabel>
        <RichTextEditor
          defaultValue={data.description}
          setValue={setDesription}
        />
        <FormErrorMessage>{errors.description}</FormErrorMessage>
      </FormControl>

      <FormControl isInvalid={!!errors.keywords}>
        <FormLabel>Keywords</FormLabel>
        <Input
          type="text"
          name="keywords"
          value={data.keywords}
          onChange={onTextChange}
          placeholder="mystery horror"
        />
        <FormErrorMessage>{errors.keywords}</FormErrorMessage>
      </FormControl>

      <FormControl isInvalid={!!errors.trial_url}>
        <FormLabel>Trial URL</FormLabel>
        <Input
          type="text"
          name="trial_url"
          value={data.trial_url}
          onChange={onTextChange}
          placeholder="https://example.com/trial"
        />
        <FormErrorMessage>{errors.trial_url}</FormErrorMessage>
      </FormControl>

      <FormControl isInvalid={!!errors.survey_url}>
        <FormLabel>Survey URL</FormLabel>
        <Input
          type="text"
          name="survey_url"
          value={data.survey_url}
          onChange={onTextChange}
          placeholder="https://example.com/survey"
        />
        <FormErrorMessage>{errors.survey_url}</FormErrorMessage>
      </FormControl>
    </>
  );
};
