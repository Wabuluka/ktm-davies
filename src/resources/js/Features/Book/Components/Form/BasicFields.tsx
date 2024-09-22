import { useBookForm } from '@/Features/Book/Hooks/useBookForm';
import { CreationList } from '@/Features/BookCreation';
import { useBookFormats } from '@/Features/BookFormat';
import { useBookSizes } from '@/Features/BookSize';
import { CreationTypeEventListenerProvider } from '@/Features/CreationType/Contexts/CreationTypeEventCallbackContext';
import { CreationType } from '@/Features/CreationType/Types';
import { CreatorEventListenerProvider } from '@/Features/Creator/Contexts/CreatorEventListnerContext';
import { SelectGenreDrawer } from '@/Features/Genre';
import { GenreSelection } from '@/Features/Genre/Components/GenreSelection';
import { SelectLabelDrawer } from '@/Features/Label';
import { LabelSelection } from '@/Features/Label/Components/LabelSelection';
import { SelectSeriesDrawer } from '@/Features/Series';
import { SeriesSelection } from '@/Features/Series/Components/SeriesSelection';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { FileInput } from '@/UI/Components/Form/Input/FileInput';
import { ResponsiveGrid } from '@/UI/Components/Layout/ResponsiveGrid';
import { ImagePreview } from '@/UI/Components/MediaAndIcons/ImagePreview';
import {
  Box,
  Button,
  Checkbox,
  CheckboxGroup,
  FormControl,
  FormErrorMessage,
  FormHelperText,
  FormLabel,
  HStack,
  Input,
  NumberInput,
  NumberInputField,
  Select,
  Text,
} from '@chakra-ui/react';
import React, { ChangeEvent, FC, useCallback } from 'react';
import { useAddCreationDrawer } from '../../../BookCreation/Hooks/useAddCreationDrawer';
import {
  useEditingBook,
  useSetEditingBook,
} from '../../Context/EditingBookContext';

type Field =
  | 'cover'
  | 'label_id'
  | 'series_id'
  | 'genre_id'
  | 'creations'
  | 'isbn13'
  | 'release_date'
  | 'price'
  | 'format_id'
  | 'size_id'
  | 'ebook_only'
  | 'special_edition'
  | 'limited_edition'
  | 'adult';

type Props = {
  data: Pick<ReturnType<typeof useBookForm>['data'], Field>;
  errors: Pick<ReturnType<typeof useBookForm>['errors'], Field>;
  setData: ReturnType<typeof useBookForm>['setData'];
};

export const BasicFields: FC<Props> = ({ data, errors, setData }) => {
  const {
    label_id,
    series_id,
    genre_id,
    creations,
    isbn13,
    release_date,
    price,
    format_id,
    size_id,
    adult,
    special_edition,
    limited_edition,
    ebook_only,
  } = data;
  const formats = useBookFormats();
  const sizes = useBookSizes();
  const editingBook = useEditingBook();
  const setEditingBook = useSetEditingBook();
  const { addCreationDrawer, addCreationDrawerOpenButton } =
    useAddCreationDrawer({ buttonLabel: 'Add' });

  const onChangeIsbn13 = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      setData('isbn13', e.target.value);
    },
    [setData],
  );

  const onChangeReleaseDate = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      setData('release_date', e.target.value);
    },
    [setData],
  );

  const onChangePrice = useCallback(
    (price: string) => {
      setData('price', price);
    },
    [setData],
  );

  const onChangeFormat = useCallback(
    (e: ChangeEvent<HTMLSelectElement>) => {
      setData('format_id', e.target.value);
    },
    [setData],
  );

  const onChangeSize = useCallback(
    (e: ChangeEvent<HTMLSelectElement>) => {
      setData('size_id', e.target.value);
    },
    [setData],
  );

  const onChangeSpecialEdition = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      setData('special_edition', e.target.checked);
    },
    [setData],
  );

  const onChangeLimitedEdition = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      setData('limited_edition', e.target.checked);
    },
    [setData],
  );

  const onChangeEbookOnly = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      setData('ebook_only', e.target.checked);
    },
    [setData],
  );

  const onChangeAdult = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) =>
      setData('adult', e.target.checked),
    [setData],
  );

  const handleCoverChange = useCallback(
    (image: File | null) => setData('cover', image),
    [setData],
  );

  const handleCoverUnselect = useCallback(() => {
    setData('cover', null);
    setEditingBook?.((book) => {
      if (!book) return book;
      return { ...book, cover: undefined };
    });
  }, [setData, setEditingBook]);

  const handleLabelChange = useCallback(
    (labelId?: number) =>
      setData('label_id', labelId ? labelId.toString() : ''),
    [setData],
  );

  const handleGenreChange = useCallback(
    (genreId?: number) =>
      setData('genre_id', genreId ? genreId.toString() : ''),
    [setData],
  );

  const handleSeriesChange = useCallback(
    (serieseId?: number) => {
      setData('series_id', serieseId ? serieseId.toString() : '');
    },
    [setData],
  );

  const handleCreatorDeleteSuccess = useCallback(
    (creatorId: string) => {
      setData((prev) => ({
        ...prev,
        creations: prev.creations
          .filter((creation) => creation.creator_id !== creatorId)
          .map((prev, i) => ({ ...prev, sort: i + 1 })),
      }));
    },
    [setData],
  );

  const handleCreationTypeUpdateSuccess = useCallback(
    (creationType: CreationType, prevTypeName: string) => {
      setData((prev) => ({
        ...prev,
        creations: prev.creations.map((creation) =>
          creation.creation_type === prevTypeName
            ? { ...creation, creation_type: creationType.name }
            : creation,
        ),
      }));
    },
    [setData],
  );

  const handleCreationTypeDeleteSuccess = useCallback(
    (creationTypeName: string) => {
      setData((prev) => ({
        ...prev,
        creations: prev.creations
          .filter((creation) => creation.creation_type !== creationTypeName)
          .map((prev, i) => ({ ...prev, sort: i + 1 })),
      }));
    },
    [setData],
  );

  return (
    <CreatorEventListenerProvider onDeleteSuccess={handleCreatorDeleteSuccess}>
      <CreationTypeEventListenerProvider
        onUpdateSuccess={handleCreationTypeUpdateSuccess}
        onDeleteSuccess={handleCreationTypeDeleteSuccess}
      >
        <FormControl>
          <FormLabel>Image</FormLabel>
          {editingBook?.cover ? (
            <ImagePreview src={editingBook.cover?.original_url}>
              <Button onClick={handleCoverUnselect}>Deselect</Button>
            </ImagePreview>
          ) : (
            <FileInput accept="image/*" onChange={handleCoverChange} />
          )}
        </FormControl>

        <Box>
          <Text mb={2}>Label</Text>
          <HStack spacing={8}>
            <SelectLabelDrawer
              onSubmit={handleLabelChange}
              selectedLabelId={Number(label_id)}
              renderOpenDrawerElement={(onOpen) => (
                <PrimaryButton onClick={onOpen}>Select</PrimaryButton>
              )}
            />
            {label_id && (
              <LabelSelection
                labelId={Number(label_id)}
                onUnselect={() => handleLabelChange()}
              />
            )}
          </HStack>
        </Box>

        <Box>
          <Text mb={2}>Genre</Text>
          <HStack spacing={8}>
            <SelectGenreDrawer
              onSubmit={handleGenreChange}
              selectedGenreId={Number(genre_id)}
              renderOpenDrawerElement={(onOpen) => (
                <PrimaryButton onClick={onOpen}>Select</PrimaryButton>
              )}
            />
            {genre_id && (
              <GenreSelection
                genreId={Number(genre_id)}
                onUnselect={() => handleGenreChange()}
              />
            )}
          </HStack>
        </Box>

        <Box>
          <Text mb={2}>Series</Text>
          <HStack spacing={8}>
            <SelectSeriesDrawer
              onSubmit={handleSeriesChange}
              selectedSeriesId={Number(series_id)}
              renderOpenDrawerElement={(onOpen) => (
                <PrimaryButton onClick={onOpen}>Select</PrimaryButton>
              )}
            />
            {series_id && (
              <SeriesSelection
                seriesId={Number(series_id)}
                onUnselect={() => handleSeriesChange()}
              />
            )}
          </HStack>
        </Box>

        <Box>
          <Text mb={2}>Author Information</Text>

          {creations.length > 0 && (
            <CreationList
              bg="gray.50"
              creations={creations}
              maxW={{ base: '100%', xl: '60%' }}
              mb={2}
            />
          )}
          {addCreationDrawerOpenButton}
        </Box>

        <ResponsiveGrid>
          <FormControl isInvalid={!!errors.isbn13}>
            <FormLabel>ISBN13</FormLabel>
            <Input
              type="text"
              name="isbn13"
              value={isbn13}
              onChange={onChangeIsbn13}
              placeholder="9784003101018"
            />
            <FormHelperText>Please input without hyphen</FormHelperText>
            <FormErrorMessage>{errors.isbn13}</FormErrorMessage>
          </FormControl>

          <FormControl isInvalid={!!errors.release_date}>
            <FormLabel>Release Date</FormLabel>
            <Input
              type="date"
              name="release_date"
              value={release_date}
              onChange={onChangeReleaseDate}
            />
            <FormErrorMessage>{errors.release_date}</FormErrorMessage>
          </FormControl>

          <FormControl isInvalid={!!errors.price}>
            <FormLabel>Price(excluding tax)</FormLabel>
            <NumberInput name="price" value={price} onChange={onChangePrice}>
              <NumberInputField />
            </NumberInput>
            <FormErrorMessage>{errors.price}</FormErrorMessage>
          </FormControl>

          <FormControl>
            <FormLabel>Format</FormLabel>
            <Select
              placeholder="Please select"
              name="format"
              value={format_id}
              onChange={onChangeFormat}
            >
              {formats.map((format) => (
                <option key={format.id} value={format.id}>
                  {format.name}
                </option>
              ))}
            </Select>
          </FormControl>

          <FormControl>
            <FormLabel>Size</FormLabel>
            <Select
              placeholder="Please select"
              name="size"
              value={size_id}
              onChange={onChangeSize}
            >
              {sizes.map((size) => (
                <option key={size.id} value={size.id}>
                  {size.name}
                </option>
              ))}
            </Select>
          </FormControl>

          <FormControl>
            <FormLabel>Flag</FormLabel>
            <CheckboxGroup>
              <HStack p={2}>
                <FormControl isInvalid={!!errors.ebook_only}>
                  <Checkbox
                    name="ebook_only"
                    defaultChecked={ebook_only}
                    onChange={onChangeEbookOnly}
                  >
                    📱 Ebook Only
                  </Checkbox>
                  <FormErrorMessage>{errors.ebook_only}</FormErrorMessage>
                </FormControl>

                <FormControl isInvalid={!!errors.special_edition}>
                  <Checkbox
                    name="special_edition"
                    defaultChecked={special_edition}
                    onChange={onChangeSpecialEdition}
                  >
                    ✨ Special Edition
                  </Checkbox>
                  <FormErrorMessage>{errors.special_edition}</FormErrorMessage>
                </FormControl>
                <FormControl isInvalid={!!errors.limited_edition}>
                  <Checkbox
                    name="limited_edition"
                    defaultChecked={limited_edition}
                    onChange={onChangeLimitedEdition}
                  >
                    🌟 Limited Edition
                  </Checkbox>
                  <FormErrorMessage>{errors.limited_edition}</FormErrorMessage>
                </FormControl>
                <FormControl isInvalid={!!errors.adult}>
                  <Checkbox
                    color="pink.800"
                    fontWeight="bold"
                    name="adult"
                    defaultChecked={adult}
                    onChange={onChangeAdult}
                  >
                    🔞 Adult
                  </Checkbox>
                  <FormErrorMessage>{errors.adult}</FormErrorMessage>
                </FormControl>
              </HStack>
            </CheckboxGroup>
          </FormControl>
        </ResponsiveGrid>

        {addCreationDrawer}
      </CreationTypeEventListenerProvider>
    </CreatorEventListenerProvider>
  );
};
