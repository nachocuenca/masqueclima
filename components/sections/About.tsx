import type { HomeDictionary } from "@/content/types";

type AboutProps = {
  dictionary: HomeDictionary;
};

export function About({ dictionary }: AboutProps) {
  const paragraphs = [
    dictionary.about.p1,
    dictionary.about.p2,
    dictionary.about.p3,
    dictionary.about.p4,
    dictionary.about.cert
  ].filter(Boolean);

  return (
    <section className="bg-[#f6f8f9] py-20" id="nosotros">
      <div className="section-shell grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
        <h2 className="text-3xl font-black text-neutral-950">{dictionary.about.title}</h2>
        <div className="space-y-5 text-lg leading-8 text-neutral-700">
          {paragraphs.map((paragraph) => (
            <p key={paragraph}>{paragraph}</p>
          ))}
        </div>
      </div>
    </section>
  );
}
