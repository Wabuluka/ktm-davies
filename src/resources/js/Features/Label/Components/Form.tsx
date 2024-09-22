import { SelectGenreDrawer } from '@/Features/Genre';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import {
  FormControl,
  FormErrorMessage,
  FormLabel,
  HStack,
  Input,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps, FC, useState } from 'react';
import { Label } from '../Types';
import { GenreSelection } from '@/Features/Genre/Components/GenreSelection';
import { LabelTypesSelectBox } from '@/Features/LabelType/Components/LabelTypeSelect';
import { LabelType, useLabelTypes } from '@/Features/LabelType';

export type formValues = {
  name: string;
  url: string;
  genre_id: string;
  type_ids: string[];
};

type Props = {
  label?: Label;
  initialFocusRef?: React.LegacyRef<HTMLInputElement>;
  errors?: Record<string, string[]>;
  onSubmit: (e: React.FormEvent<HTMLFormElement>, label: formValues) => void;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export const Form: FC<Props> = ({
  label: base,
  errors,
  initialFocusRef,
  onSubmit,
  ...props
}) => {
  const types = useLabelTypes();
  const [label, setLabel] = useState<formValues>(
    base
      ? {
          name: base.name,
          url: base.url,
          genre_id: String(base.genre_id),
          type_ids: base.types.map((type) => String(type.id)),
        }
      : {
          name: '',
          url: '',
          genre_id: '',
          type_ids: types
            .filter((type) => type.is_default)
            .map((type) => String(type.id)),
        },
  );

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setLabel((prev) => ({ ...prev, [name]: value }));
  };

  const handleGenreChange = (genreId?: number) => {
    const genre_id = genreId ? String(genreId) : '';
    setLabel((prev) => ({ ...prev, genre_id }));
  };

  const handleTypeSelect = (type: LabelType) => {
    setLabel((prev) => ({
      ...prev,
      type_ids: [...prev.type_ids, type.id.toString()],
    }));
  };

  const handleTypeUnSelect = (type: LabelType) => {
    setLabel((prev) => ({
      ...prev,
      type_ids: prev.type_ids.filter((id) => id !== type.id.toString()),
    }));
  };

  const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    onSubmit(e, label);
  };

  return (
    <form {...props} onSubmit={handleSubmit}>
      <VStack align="stretch" spacing={4}>
        <FormControl isInvalid={!!errors} isRequired>
          <FormLabel>Label Name</FormLabel>
          <Input
            ref={initialFocusRef}
            type="text"
            name="name"
            value={label?.name ?? ''}
            onChange={handleChange}
          />
          {errors?.name?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
          {errors?.label?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>

        <FormControl isInvalid={!!errors?.url}>
          <FormLabel>URL</FormLabel>
          <Input
            type="text"
            name="url"
            defaultValue={label?.url ?? ''}
            onChange={handleChange}
          />
          {errors?.url?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>

        <FormControl isInvalid={!!errors?.genre_id} isRequired>
          <FormLabel>Genre</FormLabel>
          <HStack spacing={4}>
            <SelectGenreDrawer
              onSubmit={handleGenreChange}
              selectedGenreId={Number(label.genre_id)}
              renderOpenDrawerElement={(onOpen) => (
                <PrimaryButton onClick={onOpen}>Select</PrimaryButton>
              )}
            />
            {!!label.genre_id && (
              <GenreSelection genreId={Number(label.genre_id)} />
            )}
          </HStack>
          {errors?.genre_id?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>

        <FormControl isInvalid={!!errors?.types} mt={2}>
          <FormLabel>Label Type</FormLabel>
          <LabelTypesSelectBox
            name="type_ids"
            value={label.type_ids}
            onSelect={handleTypeSelect}
            onUnselect={handleTypeUnSelect}
          />
          {errors?.url?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>
      </VStack>
    </form>
  );
};
